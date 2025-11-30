<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceTermTemplatesSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            ['name' => 'افتراضي', 'content' => "البضاعة المباعة لا تُرد ولا تُستبدل بعد 7 أيام.\nفي حال وجود عيب مصنعي يتم التعويض وفق الضمان."],
            ['name' => 'صيانة', 'content' => "خدمة الصيانة تشمل الأجزاء المذكورة فقط.\nمدة الضمان 90 يوماً من تاريخ الفاتورة."],
        ];

        $subscribers = DB::table('subscribers')->pluck('id');
        if ($subscribers->isEmpty()) {
            $subscribers = collect([null]);
        }

        foreach ($subscribers as $subscriberId) {
            foreach ($templates as $tpl) {
                DB::table('invoice_term_templates')->updateOrInsert(
                    [
                        'subscriber_id' => $subscriberId,
                        'name' => $tpl['name'],
                    ],
                    array_merge($tpl, ['subscriber_id' => $subscriberId])
                );
            }
        }
    }
}
