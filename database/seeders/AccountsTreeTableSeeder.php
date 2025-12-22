<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AccountsTree;
use App\Models\Subscriber;

class AccountsTreeTableSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['code' => '1000', 'name' => 'الأصول', 'type' => 0, 'parent_code' => '0', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '2000', 'name' => 'الخصوم', 'type' => 0, 'parent_code' => '0', 'list' => 1, 'department' => 1, 'side' => 2],
            ['code' => '3000', 'name' => 'حقوق الملكية', 'type' => 0, 'parent_code' => '0', 'list' => 1, 'department' => 1, 'side' => 2],
            ['code' => '4000', 'name' => 'الإيرادات', 'type' => 0, 'parent_code' => '0', 'list' => 3, 'department' => 2, 'side' => 2],
            ['code' => '5000', 'name' => 'المصروفات', 'type' => 0, 'parent_code' => '0', 'list' => 4, 'department' => 2, 'side' => 1],
            ['code' => '52', 'name' => 'تكاليف تشغيلية', 'type' => 0, 'parent_code' => '0', 'list' => 4, 'department' => 2, 'side' => 1],

            ['code' => '1100', 'name' => 'النقدية وما في حكمها', 'type' => 1, 'parent_code' => '1000', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '1101', 'name' => 'الصندوق', 'type' => 2, 'parent_code' => '1100', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '110101', 'name' => 'خزنة رئيسية', 'type' => 3, 'parent_code' => '1101', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '1102', 'name' => 'البنوك', 'type' => 2, 'parent_code' => '1100', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '110201', 'name' => 'بنك رئيسي', 'type' => 3, 'parent_code' => '1102', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '1107', 'name' => 'العملاء', 'type' => 2, 'parent_code' => '1000', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '110701', 'name' => 'عميل عام', 'type' => 3, 'parent_code' => '1107', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '1201', 'name' => 'المخزون', 'type' => 2, 'parent_code' => '1000', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '120101', 'name' => 'مخزون بضائع', 'type' => 3, 'parent_code' => '1201', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '1301', 'name' => 'ضريبة القيمة المضافة (مدخلات)', 'type' => 3, 'parent_code' => '1000', 'list' => 1, 'department' => 1, 'side' => 1],

            ['code' => '2100', 'name' => 'الدائنون', 'type' => 1, 'parent_code' => '2000', 'list' => 1, 'department' => 1, 'side' => 2],
            ['code' => '2101', 'name' => 'الموردون', 'type' => 2, 'parent_code' => '2100', 'list' => 1, 'department' => 1, 'side' => 2],
            ['code' => '210101', 'name' => 'مورد عام', 'type' => 3, 'parent_code' => '2101', 'list' => 1, 'department' => 1, 'side' => 2],
            ['code' => '2102', 'name' => 'ضريبة القيمة المضافة (مخرجات)', 'type' => 3, 'parent_code' => '2000', 'list' => 1, 'department' => 1, 'side' => 2],
            ['code' => '2103', 'name' => 'ضريبة مخرجات إضافية', 'type' => 3, 'parent_code' => '2000', 'list' => 1, 'department' => 1, 'side' => 2],

            ['code' => '3101', 'name' => 'أرباح وخسائر', 'type' => 3, 'parent_code' => '3000', 'list' => 1, 'department' => 1, 'side' => 2],

            ['code' => '4101', 'name' => 'المبيعات', 'type' => 3, 'parent_code' => '4000', 'list' => 3, 'department' => 2, 'side' => 2],
            ['code' => '4102', 'name' => 'مرتجع المبيعات', 'type' => 3, 'parent_code' => '4000', 'list' => 3, 'department' => 2, 'side' => 2],
            ['code' => '4103', 'name' => 'خصم المبيعات', 'type' => 3, 'parent_code' => '4000', 'list' => 3, 'department' => 2, 'side' => 2],

            ['code' => '5101', 'name' => 'المشتريات', 'type' => 3, 'parent_code' => '5000', 'list' => 4, 'department' => 2, 'side' => 1],
            ['code' => '5102', 'name' => 'مرتجع المشتريات', 'type' => 3, 'parent_code' => '5000', 'list' => 4, 'department' => 2, 'side' => 1],
            ['code' => '5103', 'name' => 'خصم المشتريات', 'type' => 3, 'parent_code' => '5000', 'list' => 4, 'department' => 2, 'side' => 1],
            ['code' => '5201', 'name' => 'تكلفة البضاعة المباعة', 'type' => 3, 'parent_code' => '52', 'list' => 4, 'department' => 2, 'side' => 1],
            ['code' => '5202', 'name' => 'عكس الربح', 'type' => 3, 'parent_code' => '52', 'list' => 4, 'department' => 2, 'side' => 1],
            ['code' => '5301', 'name' => 'مصروفات تشغيلية', 'type' => 3, 'parent_code' => '5000', 'list' => 4, 'department' => 2, 'side' => 1],
        ];

        $subscriberIds = Subscriber::pluck('id');
        if ($subscriberIds->isEmpty()) {
            $subscriberIds = collect([null]);
        }

        foreach ($subscriberIds as $subscriberId) {
            foreach ($accounts as $acc) {
                $parentId = 0;
                $level = 1;
                if (!empty($acc['parent_code']) && $acc['parent_code'] !== '0') {
                    $parent = AccountsTree::withoutGlobalScope('subscriber')
                        ->where('code', $acc['parent_code'])
                        ->when($subscriberId !== null, function ($q) use ($subscriberId) {
                            $q->where('subscriber_id', $subscriberId);
                        })
                        ->first();
                    if ($parent) {
                        $parentId = $parent->id;
                        $level = ($parent->level ?? 1) + 1;
                    }
                }

                $payload = array_merge($acc, [
                    'parent_id' => $parentId,
                    'parent_code' => $acc['parent_code'] ?? '0',
                    'level' => $level,
                    'subscriber_id' => $subscriberId,
                    'is_active' => 1,
                ]);

                AccountsTree::updateOrCreate(
                    [
                        'code' => $acc['code'],
                        'subscriber_id' => $subscriberId,
                    ],
                    $payload
                );
            }
        }
    }
}
