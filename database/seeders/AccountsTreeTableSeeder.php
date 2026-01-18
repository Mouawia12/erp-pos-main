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
            ['code' => '52', 'name' => 'تكاليف التشغيل', 'type' => 1, 'parent_code' => '5000', 'list' => 4, 'department' => 2, 'side' => 1],

            ['code' => '1100', 'name' => 'الأصول المتداولة', 'type' => 1, 'parent_code' => '1000', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '1101', 'name' => 'النقدية', 'type' => 2, 'parent_code' => '1100', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '110101', 'name' => 'صندوق رئيسي', 'type' => 3, 'parent_code' => '1101', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '110102', 'name' => 'عهد نقدية', 'type' => 3, 'parent_code' => '1101', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '1102', 'name' => 'البنوك', 'type' => 2, 'parent_code' => '1100', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '110201', 'name' => 'حساب بنكي رئيسي', 'type' => 3, 'parent_code' => '1102', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '1103', 'name' => 'شيكات تحت التحصيل', 'type' => 2, 'parent_code' => '1100', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '110301', 'name' => 'شيكات عملاء', 'type' => 3, 'parent_code' => '1103', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '1107', 'name' => 'العملاء', 'type' => 2, 'parent_code' => '1100', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '110701', 'name' => 'عميل عام', 'type' => 3, 'parent_code' => '1107', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '1108', 'name' => 'ذمم أخرى', 'type' => 2, 'parent_code' => '1100', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '110801', 'name' => 'سلف موظفين', 'type' => 3, 'parent_code' => '1108', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '1200', 'name' => 'المخزون', 'type' => 1, 'parent_code' => '1000', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '1201', 'name' => 'مخزون بضائع', 'type' => 2, 'parent_code' => '1200', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '120101', 'name' => 'مخزون مستودع رئيسي', 'type' => 3, 'parent_code' => '1201', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '1300', 'name' => 'مصروفات مقدمة', 'type' => 1, 'parent_code' => '1000', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '1301', 'name' => 'ضريبة القيمة المضافة (مدخلات)', 'type' => 2, 'parent_code' => '1300', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '1400', 'name' => 'الأصول غير المتداولة', 'type' => 1, 'parent_code' => '1000', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '1410', 'name' => 'الممتلكات والمعدات', 'type' => 2, 'parent_code' => '1400', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '1411', 'name' => 'مبان', 'type' => 3, 'parent_code' => '1410', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '1412', 'name' => 'معدات', 'type' => 3, 'parent_code' => '1410', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '1413', 'name' => 'مركبات', 'type' => 3, 'parent_code' => '1410', 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '1490', 'name' => 'مجمع الإهلاك', 'type' => 2, 'parent_code' => '1400', 'list' => 1, 'department' => 1, 'side' => 2],

            ['code' => '2100', 'name' => 'الخصوم المتداولة', 'type' => 1, 'parent_code' => '2000', 'list' => 1, 'department' => 1, 'side' => 2],
            ['code' => '2101', 'name' => 'الموردون', 'type' => 2, 'parent_code' => '2100', 'list' => 1, 'department' => 1, 'side' => 2],
            ['code' => '210101', 'name' => 'مورد عام', 'type' => 3, 'parent_code' => '2101', 'list' => 1, 'department' => 1, 'side' => 2],
            ['code' => '2102', 'name' => 'ضريبة القيمة المضافة (مخرجات)', 'type' => 2, 'parent_code' => '2100', 'list' => 1, 'department' => 1, 'side' => 2],
            ['code' => '2103', 'name' => 'مصروفات مستحقة', 'type' => 2, 'parent_code' => '2100', 'list' => 1, 'department' => 1, 'side' => 2],
            ['code' => '2104', 'name' => 'رواتب مستحقة', 'type' => 3, 'parent_code' => '2103', 'list' => 1, 'department' => 1, 'side' => 2],
            ['code' => '2200', 'name' => 'قروض والتزامات طويلة الأجل', 'type' => 1, 'parent_code' => '2000', 'list' => 1, 'department' => 1, 'side' => 2],
            ['code' => '2201', 'name' => 'قروض بنكية', 'type' => 2, 'parent_code' => '2200', 'list' => 1, 'department' => 1, 'side' => 2],
            ['code' => '220101', 'name' => 'قرض بنكي رئيسي', 'type' => 3, 'parent_code' => '2201', 'list' => 1, 'department' => 1, 'side' => 2],

            ['code' => '3100', 'name' => 'رأس المال', 'type' => 1, 'parent_code' => '3000', 'list' => 1, 'department' => 1, 'side' => 2],
            ['code' => '3101', 'name' => 'رأس المال - المالك', 'type' => 2, 'parent_code' => '3100', 'list' => 1, 'department' => 1, 'side' => 2],
            ['code' => '3200', 'name' => 'الأرباح المحتجزة', 'type' => 1, 'parent_code' => '3000', 'list' => 1, 'department' => 1, 'side' => 2],
            ['code' => '3201', 'name' => 'أرباح وخسائر مرحّلة', 'type' => 2, 'parent_code' => '3200', 'list' => 1, 'department' => 1, 'side' => 2],

            ['code' => '4100', 'name' => 'إيرادات المبيعات', 'type' => 1, 'parent_code' => '4000', 'list' => 3, 'department' => 2, 'side' => 2],
            ['code' => '4101', 'name' => 'مبيعات', 'type' => 2, 'parent_code' => '4100', 'list' => 3, 'department' => 2, 'side' => 2],
            ['code' => '4102', 'name' => 'مرتجع المبيعات', 'type' => 2, 'parent_code' => '4100', 'list' => 3, 'department' => 2, 'side' => 2],
            ['code' => '4103', 'name' => 'خصم المبيعات', 'type' => 2, 'parent_code' => '4100', 'list' => 3, 'department' => 2, 'side' => 2],
            ['code' => '4200', 'name' => 'إيرادات أخرى', 'type' => 1, 'parent_code' => '4000', 'list' => 3, 'department' => 2, 'side' => 2],
            ['code' => '4201', 'name' => 'إيرادات خدمات', 'type' => 2, 'parent_code' => '4200', 'list' => 3, 'department' => 2, 'side' => 2],
            ['code' => '4202', 'name' => 'إيرادات متنوعة', 'type' => 2, 'parent_code' => '4200', 'list' => 3, 'department' => 2, 'side' => 2],

            ['code' => '5100', 'name' => 'تكلفة المبيعات', 'type' => 1, 'parent_code' => '5000', 'list' => 4, 'department' => 2, 'side' => 1],
            ['code' => '5101', 'name' => 'تكلفة البضاعة المباعة', 'type' => 2, 'parent_code' => '5100', 'list' => 4, 'department' => 2, 'side' => 1],
            ['code' => '5102', 'name' => 'مرتجع المشتريات', 'type' => 2, 'parent_code' => '5100', 'list' => 4, 'department' => 2, 'side' => 1],
            ['code' => '5103', 'name' => 'خصم المشتريات', 'type' => 2, 'parent_code' => '5100', 'list' => 4, 'department' => 2, 'side' => 1],
            ['code' => '5200', 'name' => 'مصروفات تشغيلية', 'type' => 1, 'parent_code' => '52', 'list' => 4, 'department' => 2, 'side' => 1],
            ['code' => '5201', 'name' => 'رواتب وأجور', 'type' => 2, 'parent_code' => '5200', 'list' => 4, 'department' => 2, 'side' => 1],
            ['code' => '5202', 'name' => 'إيجارات', 'type' => 2, 'parent_code' => '5200', 'list' => 4, 'department' => 2, 'side' => 1],
            ['code' => '5203', 'name' => 'كهرباء ومياه', 'type' => 2, 'parent_code' => '5200', 'list' => 4, 'department' => 2, 'side' => 1],
            ['code' => '5204', 'name' => 'تسويق وإعلان', 'type' => 2, 'parent_code' => '5200', 'list' => 4, 'department' => 2, 'side' => 1],
            ['code' => '5205', 'name' => 'إهلاك', 'type' => 2, 'parent_code' => '5200', 'list' => 4, 'department' => 2, 'side' => 1],
            ['code' => '5206', 'name' => 'صيانة', 'type' => 2, 'parent_code' => '5200', 'list' => 4, 'department' => 2, 'side' => 1],
            ['code' => '5207', 'name' => 'مصروفات إدارية', 'type' => 2, 'parent_code' => '5200', 'list' => 4, 'department' => 2, 'side' => 1],
            ['code' => '5208', 'name' => 'نقل وشحن', 'type' => 2, 'parent_code' => '5200', 'list' => 4, 'department' => 2, 'side' => 1],
            ['code' => '5209', 'name' => 'رسوم بنكية', 'type' => 2, 'parent_code' => '5200', 'list' => 4, 'department' => 2, 'side' => 1],
            ['code' => '5210', 'name' => 'تأمين', 'type' => 2, 'parent_code' => '5200', 'list' => 4, 'department' => 2, 'side' => 1],
            ['code' => '5211', 'name' => 'اتصالات', 'type' => 2, 'parent_code' => '5200', 'list' => 4, 'department' => 2, 'side' => 1],
            ['code' => '5212', 'name' => 'أتعاب مهنية', 'type' => 2, 'parent_code' => '5200', 'list' => 4, 'department' => 2, 'side' => 1],
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
