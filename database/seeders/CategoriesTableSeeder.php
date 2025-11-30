<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoriesTableSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            ['name' => 'معدات عامة', 'description' => 'فئة افتراضية للأصناف العامة', 'tax_excise' => 0],
            ['name' => 'الكترونيات', 'description' => 'أجهزة وشواحن واكسسوارات', 'tax_excise' => 0],
            ['name' => 'مواد غذائية', 'description' => 'سلع استهلاكية', 'tax_excise' => 0],
        ];

        $subscribers = DB::table('subscribers')
            ->select('id', 'company_name')
            ->get();

        if ($subscribers->isEmpty()) {
            $subscribers = collect([(object) ['id' => null, 'company_name' => 'مشترك افتراضي']]);
        }

        foreach ($subscribers as $subscriber) {
            $branchId = DB::table('branches')
                ->where('subscriber_id', $subscriber->id)
                ->value('id') ?? DB::table('branches')->value('id');

            $userId = DB::table('users')
                ->where('subscriber_id', $subscriber->id)
                ->value('id') ?? DB::table('users')->value('id');

            if (! $branchId || ! $userId) {
                continue;
            }

            foreach ($templates as $index => $template) {
                $code = sprintf('CAT-%02d-%02d', $subscriber->id ?? 0, $index + 1);
                DB::table('categories')->updateOrInsert(
                    [
                        'subscriber_id' => $subscriber->id,
                        'code' => $code,
                    ],
                    [
                        'name' => $template['name'] . ' - ' . ($subscriber->company_name ?? 'مشترك'),
                        'slug' => Str::slug($code),
                        'description' => $template['description'],
                        'parent_id' => 0,
                        'tax_excise' => $template['tax_excise'],
                        'branch_id' => $branchId,
                        'user_id' => $userId,
                        'status' => 1,
                        'subscriber_id' => $subscriber->id,
                    ]
                );
            }
        }
    }
}
