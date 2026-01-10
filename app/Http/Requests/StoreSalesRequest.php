<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSalesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'invoice_no' => 'nullable|string|max:255',
            'customer_id' => 'required|integer|exists:companies,id',
            'warehouse_id' => 'required|integer|exists:warehouses,id',
            'branch_id' => 'nullable|integer|exists:branches,id',
            'service_mode' => 'nullable|in:dine_in,takeaway,delivery',
            'session_location' => 'nullable|string|max:255',
            'session_type' => 'nullable|string|max:191',
            'reservation_enabled' => 'nullable|boolean',
            'reservation_time' => 'nullable|date',
            'reservation_guests' => 'nullable|integer|min:1|max:9999',
            'pos_section_id' => 'nullable|integer|exists:pos_sections,id',
            'pos_reservation_id' => 'nullable|integer|exists:pos_reservations,id',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|integer|exists:products,id',
            'qnt' => 'required|array|min:1',
            'qnt.*' => 'required|numeric|min:0.0001',
            'price_unit' => 'required|array|min:1',
            'price_unit.*' => 'required|numeric|min:0',
            'original_price' => 'nullable|array',
            'original_price.*' => 'nullable|numeric|min:0',
            'unit_id' => 'nullable|array',
            'unit_id.*' => 'nullable|integer|exists:units,id',
            'unit_factor' => 'nullable|array',
            'unit_factor.*' => 'nullable|numeric|min:0.0001',
            'discount_unit' => 'nullable|array',
            'discount_unit.*' => 'nullable|numeric|min:0',
            'reservation_item_id' => 'nullable|array',
            'reservation_item_id.*' => 'nullable|integer',
            'salon_reservation_id' => 'nullable|array',
            'salon_reservation_id.*' => 'nullable|integer',
            'quotation_id' => 'nullable|array',
            'quotation_id.*' => 'nullable|integer',
            'additional_service' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'cash' => 'nullable|numeric|min:0',
            'card_amount' => 'nullable|array',
            'card_amount.*' => 'nullable|numeric|min:0',
            'vehicle_plate' => 'nullable|string|max:255',
            'vehicle_odometer' => 'nullable|numeric|min:0',
            'customer_name' => 'nullable|string|max:191',
            'customer_phone' => 'nullable|string|max:191',
            'customer_address' => 'nullable|string|max:255',
            'customer_tax_number' => 'nullable|string|max:191',
            'notes' => 'nullable|string|max:5000',
        ];
    }

    public function messages()
    {
        return [
            'product_id.required' => __('main.invoice_details_required') ?? 'يجب إضافة صنف واحد على الأقل',
            'product_id.*.exists' => 'تم اختيار صنف غير موجود',
            'qnt.*.min' => 'الكمية يجب أن تكون أكبر من صفر',
        ];
    }
}
