<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandsTableSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            ['name' => 'منتجات عامة', 'status' => 1],
            ['name' => 'مستلزمات الكترونية', 'status' => 1],
            ['name' => 'مواد غذائية', 'status' => 1],
        ];

        $subscribers = DB::table('subscribers')
            ->select('id', 'company_name')
            ->get();

        if ($subscribers->isEmpty()) {
            $subscribers = collect([(object) ['id' => null, 'company_name' => 'مشترك افتراضي']]);
        }

        foreach ($subscribers as $subscriber) {
            $userId = DB::table('users')
                ->where('subscriber_id', $subscriber->id)
                ->value('id') ?? DB::table('users')->value('id');

            foreach ($templates as $index => $template) {
                DB::table('brands')->updateOrInsert(
                    [
                        'subscriber_id' => $subscriber->id,
                        'code' => sprintf('BR-%02d-%02d', $subscriber->id ?? 0, $index + 1),
                    ],
                    [
                        'name' => $template['name'] . ' - ' . ($subscriber->company_name ?? 'مشترك'),
                        'user_id' => $userId,
                        'status' => $template['status'],
                        'subscriber_id' => $subscriber->id,
                    ]
                );
            }
        }
    }
}
