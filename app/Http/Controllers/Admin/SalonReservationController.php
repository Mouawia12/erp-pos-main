<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\SalonDepartment;
use App\Models\SalonReservation;
use App\Models\SalonReservationItem;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseProducts;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SalonReservationController extends Controller
{
    public function index()
    {
        $reservations = SalonReservation::with(['customer', 'department', 'assignedUser'])
            ->when(Auth::user()->subscriber_id ?? null, fn($q,$v) => $q->where('subscriber_id', $v))
            ->orderByDesc('reservation_time')
            ->get();

        $customers = Company::query()
            ->where('group_id', 3)
            ->when(Auth::user()->subscriber_id ?? null, fn($q,$v) => $q->where('subscriber_id', $v))
            ->orderBy('name')
            ->get();

        $departments = SalonDepartment::query()
            ->when(Auth::user()->subscriber_id ?? null, fn($q,$v) => $q->where('subscriber_id', $v))
            ->orderBy('name')
            ->get();

        $users = User::query()
            ->when(Auth::user()->subscriber_id ?? null, fn($q,$v) => $q->where('subscriber_id', $v))
            ->orderBy('name')
            ->get();

        $warehouses = Warehouse::query()
            ->when(Auth::user()->subscriber_id ?? null, fn($q,$v) => $q->where('subscriber_id', $v))
            ->orderBy('name')
            ->get();

        $products = Product::query()
            ->where('status', 1)
            ->when(Auth::user()->subscriber_id ?? null, fn($q,$v) => $q->where('subscriber_id', $v))
            ->orderBy('name')
            ->get();

        return view('admin.salon.reservations', compact('reservations', 'customers', 'departments', 'users', 'warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:companies,id'],
            'salon_department_id' => ['nullable', 'exists:salon_departments,id'],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'reservation_time' => ['required', 'date'],
            'location_text' => ['nullable', 'string', 'max:191'],
            'location_url' => ['nullable', 'string', 'max:191'],
            'status' => ['nullable', 'in:scheduled,completed,cancelled,invoiced'],
            'notes' => ['nullable', 'string'],
            'item_product_id' => ['required', 'array', 'min:1'],
            'item_product_id.*' => ['required', 'integer', 'exists:products,id'],
            'item_qty' => ['required', 'array', 'min:1'],
            'item_qty.*' => ['required', 'numeric', 'min:0.0001'],
        ]);

        $subscriberId = Auth::user()->subscriber_id ?? null;
        $warehouseId = (int) $data['warehouse_id'];
        $stockItems = [];
        $reservationItems = [];

        foreach ($data['item_product_id'] as $index => $productId) {
            $qty = (float) ($data['item_qty'][$index] ?? 0);
            if ($qty <= 0) {
                continue;
            }
            $product = Product::find($productId);
            if (!$product) {
                throw ValidationException::withMessages([
                    "item_product_id.$index" => __('main.product_not_found') ?? 'تم اختيار صنف غير موجود'
                ]);
            }
            $unitId = $product->unit;
            $unitFactor = 1;
            $reservationItems[] = [
                'product_id' => $productId,
                'variant_id' => null,
                'unit_id' => $unitId,
                'unit_factor' => $unitFactor,
                'quantity' => $qty,
                'note' => null,
                'subscriber_id' => $subscriberId,
            ];

            if ($this->shouldReserveStock($product)) {
                $this->ensureWarehouseStock($warehouseId, $product);
                $availableQty = (float) (WarehouseProducts::query()
                    ->where('warehouse_id', $warehouseId)
                    ->where('product_id', $productId)
                    ->value('quantity') ?? 0);
                $requiredQty = $qty * $unitFactor;
                if ($availableQty < $requiredQty && !$this->allowNegativeStock()) {
                    throw ValidationException::withMessages([
                        "item_product_id.$index" => __('main.insufficient_stock', ['item' => $product->name])
                    ]);
                }
                $item = new Product();
                $item->product_id = $productId;
                $item->quantity = $requiredQty;
                $item->warehouse_id = $warehouseId;
                $stockItems[] = $item;
            }
        }

        $reservation = SalonReservation::create([
            'customer_id' => $data['customer_id'],
            'salon_department_id' => $data['salon_department_id'] ?? null,
            'assigned_user_id' => $data['assigned_user_id'] ?? null,
            'warehouse_id' => $data['warehouse_id'],
            'reservation_time' => $data['reservation_time'],
            'location_text' => $data['location_text'] ?? null,
            'location_url' => $data['location_url'] ?? null,
            'status' => $data['status'] ?? 'scheduled',
            'notes' => $data['notes'] ?? null,
            'subscriber_id' => Auth::user()->subscriber_id,
            'branch_id' => Auth::user()->branch_id,
            'created_by' => Auth::id(),
        ]);

        if ($reservationItems) {
            foreach ($reservationItems as $payload) {
                $payload['salon_reservation_id'] = $reservation->id;
                SalonReservationItem::create($payload);
            }
        }

        if ($stockItems) {
            $siteController = new SystemController();
            $siteController->syncQnt($stockItems, null, true);
        }

        if (empty($reservation->reservation_no)) {
            $reservation->update([
                'reservation_no' => $this->buildReservationNo($reservation->id, $reservation->branch_id),
            ]);
        }

        return redirect()->route('salon.reservations')->with('success', __('main.created') ?? 'تم اضافة عنصر جديد بنجاح');
    }

    public function update(Request $request, $id)
    {
        $reservation = SalonReservation::findOrFail($id);

        $data = $request->validate([
            'customer_id' => ['required', 'exists:companies,id'],
            'salon_department_id' => ['nullable', 'exists:salon_departments,id'],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'reservation_time' => ['required', 'date'],
            'location_text' => ['nullable', 'string', 'max:191'],
            'location_url' => ['nullable', 'string', 'max:191'],
            'status' => ['nullable', 'in:scheduled,completed,cancelled,invoiced'],
            'notes' => ['nullable', 'string'],
        ]);

        $reservation->update([
            'customer_id' => $data['customer_id'],
            'salon_department_id' => $data['salon_department_id'] ?? null,
            'assigned_user_id' => $data['assigned_user_id'] ?? null,
            'warehouse_id' => $data['warehouse_id'],
            'reservation_time' => $data['reservation_time'],
            'location_text' => $data['location_text'] ?? null,
            'location_url' => $data['location_url'] ?? null,
            'status' => $data['status'] ?? 'scheduled',
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('salon.reservations')->with('success', __('main.updated') ?? 'تم التعديل بنجاح');
    }

    public function destroy($id)
    {
        $reservation = SalonReservation::findOrFail($id);
        if (empty($reservation->sale_id) && $reservation->status !== 'invoiced') {
            $stockItems = [];
            $items = SalonReservationItem::query()
                ->where('salon_reservation_id', $reservation->id)
                ->get();
            foreach ($items as $item) {
                $product = Product::find($item->product_id);
                if (!$product || !$this->shouldReserveStock($product)) {
                    continue;
                }
                $restock = new Product();
                $restock->product_id = $item->product_id;
                $restock->quantity = $item->quantity * ($item->unit_factor ?? 1);
                $restock->warehouse_id = $reservation->warehouse_id;
                $stockItems[] = $restock;
            }
            if ($stockItems) {
                $siteController = new SystemController();
                $siteController->syncQnt($stockItems, null, false);
            }
        }
        SalonReservationItem::query()
            ->where('salon_reservation_id', $reservation->id)
            ->delete();
        $reservation->delete();

        return redirect()->route('salon.reservations')->with('success', __('main.deleted') ?? 'تم حذف البيانات بنجاح');
    }

    public function customerReservations(Company $customer)
    {
        $subscriberId = Auth::user()->subscriber_id ?? null;
        if ($subscriberId && (int) $customer->subscriber_id !== (int) $subscriberId) {
            abort(403);
        }

        $reservations = SalonReservation::with(['items.product', 'department'])
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->where('customer_id', $customer->id)
            ->whereNull('sale_id')
            ->where('status', 'scheduled')
            ->orderBy('reservation_time')
            ->get();

        $payload = $reservations->map(function (SalonReservation $reservation) {
            return [
                'id' => $reservation->id,
                'reservation_no' => $reservation->reservation_no,
                'reservation_time' => $reservation->reservation_time ? \Carbon\Carbon::parse($reservation->reservation_time)->format('Y-m-d H:i') : null,
                'department' => $reservation->department?->name,
                'warehouse_id' => $reservation->warehouse_id,
                'items' => $reservation->items->map(function (SalonReservationItem $item) use ($reservation) {
                    $product = $item->product;
                    if (!$product) {
                        return null;
                    }
                    $unitName = $item->unit_id ? optional(Unit::find($item->unit_id))->name : optional(Unit::find($product->unit))->name;
                    $availableQty = WarehouseProducts::query()
                        ->where('warehouse_id', $reservation->warehouse_id)
                        ->where('product_id', $product->id)
                        ->value('quantity') ?? 0;
                    return [
                        'reservation_item_id' => $item->id,
                        'salon_reservation_id' => $reservation->id,
                        'quantity' => (float) $item->quantity,
                        'unit_id' => $item->unit_id ?? $product->unit,
                        'unit_factor' => (float) ($item->unit_factor ?? 1),
                        'product' => [
                            'id' => $product->id,
                            'name' => $product->name,
                            'code' => $product->code,
                            'price' => $product->price,
                            'tax_method' => $product->tax_method,
                            'total_tax_rate' => $product->totalTaxRate(),
                            'tax_excise' => $product->tax_excise,
                            'price_includes_tax' => $product->price_includes_tax,
                            'qty' => (float) $availableQty,
                            'unit' => $product->unit,
                            'units_options' => [[
                                'unit_id' => $item->unit_id ?? $product->unit,
                                'unit_name' => $unitName ?: '',
                                'price' => $product->price,
                                'conversion_factor' => (float) ($item->unit_factor ?? 1),
                                'barcode' => null,
                            ]],
                            'shipping_service_type' => $product->shipping_service_type,
                            'shipping_service_amount' => $product->shipping_service_amount,
                            'delivery_service_type' => $product->delivery_service_type,
                            'delivery_service_amount' => $product->delivery_service_amount,
                            'installation_service_type' => $product->installation_service_type,
                            'installation_service_amount' => $product->installation_service_amount,
                            'promo_discount_unit' => 0,
                        ],
                    ];
                })->filter()->values(),
            ];
        })->values();

        return response()->json($payload);
    }

    private function shouldReserveStock(Product $product): bool
    {
        $trackQuantity = (int) ($product->track_quantity ?? 0) === 1;
        $isService = (int) ($product->type ?? 1) === 3;
        return $trackQuantity && !$isService;
    }

    private function allowNegativeStock(): bool
    {
        $siteController = new SystemController();
        return $siteController->allowSellingWithoutStock();
    }

    private function ensureWarehouseStock(int $warehouseId, Product $product): void
    {
        $record = WarehouseProducts::query()
            ->where('warehouse_id', $warehouseId)
            ->where('product_id', $product->id)
            ->first();

        if ($record) {
            return;
        }

        WarehouseProducts::create([
            'warehouse_id' => $warehouseId,
            'product_id' => $product->id,
            'quantity' => $product->quantity ?? 0,
            'cost' => $product->cost,
            'price' => $product->price,
        ]);
    }

    private function buildReservationNo(int $id, ?int $branchId): string
    {
        $prefix = $branchId ? ('SR-' . $branchId . '-') : 'SR-';
        return $prefix . str_pad($id, 6, '0', STR_PAD_LEFT);
    }
}
