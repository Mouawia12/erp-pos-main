<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSubscriber;

class Company extends Model
{
    use HasFactory, BelongsToSubscriber;
    protected $fillable = [
        'group_id',
        'group_name',
        'customer_group_id',
        'customer_group_name',
        'name',
        'company',
        'vat_no',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'email',
        'phone',
        'invoice_footer',
        'logo',
        'award_points',
        'deposit_amount',
        'opening_balance',
        'account_id',
        'credit_amount',
        'stop_sale',
        'representative_id_',
        'user_id',
        'status',
        'cr_number',
        'tax_number',
        'parent_company_id',
        'price_level_id',
        'default_discount',
        'subscriber_id',
        'is_walk_in',
    ];

    public function group()
    {
        return $this->belongsTo(CustomerGroup::class , 'customer_group_id');
    }

    public static function ensureWalkInCustomer(?int $subscriberId = null): ?self
    {
        $subscriberId = $subscriberId ?? auth()->user()->subscriber_id ?? null;

        $query = static::query()
            ->where('group_id', 3)
            ->where('is_walk_in', 1);

        if ($subscriberId) {
            $query->where('subscriber_id', $subscriberId);
        }

        $existing = $query->first();
        if ($existing) {
            return $existing;
        }

        $data = [
            'group_id' => 3,
            'group_name' => 'customer',
            'customer_group_id' => 0,
            'customer_group_name' => __('main.General') ?? 'عام',
            'name' => __('main.default_walk_in_customer') ?? 'عميل نقدي افتراضي',
            'company' => null,
            'vat_no' => 'N/A',
            'address' => '',
            'city' => 'N/A',
            'state' => 'N/A',
            'postal_code' => '',
            'country' => 'N/A',
            'email' => null,
            'phone' => null,
            'invoice_footer' => null,
            'logo' => null,
            'award_points' => 0,
            'deposit_amount' => 0,
            'opening_balance' => 0,
            'account_id' => 0,
            'credit_amount' => 0,
            'stop_sale' => 0,
            'representative_id_' => 0,
            'user_id' => auth()->id() ?? 0,
            'status' => 1,
            'cr_number' => null,
            'tax_number' => null,
            'parent_company_id' => null,
            'price_level_id' => null,
            'default_discount' => 0,
            'is_walk_in' => 1,
        ];

        if ($subscriberId) {
            $data['subscriber_id'] = $subscriberId;
        }

        return static::create($data);
    }
}
