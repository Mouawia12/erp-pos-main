<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Subscriber;
use App\Models\Warehouse;

class SubscriberProvisioner
{
    /**
     * Ensure the subscriber owns at least one branch and one warehouse.
     *
     * @return array{0: \App\Models\Branch, 1: \App\Models\Warehouse|null}
     */
    public static function ensureDefaults(Subscriber $subscriber): array
    {
        $branch = Branch::where('subscriber_id', $subscriber->id)
            ->orderBy('id')
            ->first();

        if (! $branch) {
            $branch = Branch::create([
                'branch_name' => 'فرع رئيسي - ' . $subscriber->company_name,
                'cr_number' => $subscriber->cr_number,
                'tax_number' => $subscriber->tax_number,
                'branch_phone' => $subscriber->contact_phone ?? '0000000000',
                'branch_address' => $subscriber->address,
                'manager_name' => $subscriber->responsible_person ?? $subscriber->company_name,
                'contact_email' => $subscriber->contact_email ?? $subscriber->login_email,
                'default_invoice_type' => 'simplified_tax_invoice',
                'status' => 1,
                'subscriber_id' => $subscriber->id,
            ]);
        }

        $warehouse = Warehouse::where('subscriber_id', $subscriber->id)
            ->orderBy('id')
            ->first();

        if (! $warehouse) {
            $warehouse = Warehouse::create([
                'code' => 'WH-' . $subscriber->id,
                'name' => 'مستودع رئيسي - ' . $subscriber->company_name,
                'phone' => $subscriber->contact_phone ?? '0000000000',
                'email' => $subscriber->contact_email ?? $subscriber->login_email,
                'address' => $subscriber->address,
                'tax_number' => $subscriber->tax_number,
                'commercial_registration' => $subscriber->cr_number,
                'serial_prefix' => 'WH' . str_pad((string) $subscriber->id, 2, '0', STR_PAD_LEFT),
                'branch_id' => $branch->id,
                'user_id' => $subscriber->user_id ?? 0,
                'status' => 1,
                'subscriber_id' => $subscriber->id,
            ]);
        } elseif (! $warehouse->branch_id) {
            $warehouse->update(['branch_id' => $branch->id]);
        }

        return [$branch, $warehouse];
    }
}
