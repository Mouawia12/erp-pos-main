<?php

namespace Database\Seeders;

use App\Models\InvoiceTermTemplate;
use Illuminate\Database\Seeder;

class InvoiceTermTemplatesSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            ['name' => 'افتراضي', 'content' => "البضاعة المباعة لا تُرد ولا تُستبدل بعد 7 أيام.\nفي حال وجود عيب مصنعي يتم التعويض وفق الضمان."],
            ['name' => 'صيانة', 'content' => "خدمة الصيانة تشمل الأجزاء المذكورة فقط.\nمدة الضمان 90 يوماً من تاريخ الفاتورة."],
        ];

        foreach ($templates as $tpl) {
            InvoiceTermTemplate::updateOrCreate(['name' => $tpl['name']], $tpl);
        }
    }
}
