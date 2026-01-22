<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Company;
use App\Models\CompanyInfo;
use App\Models\User;
use App\Models\VendorMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VendorBalanceReportTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(): User
    {
        return User::create([
            'name' => 'Vendor Report User',
            'email' => 'vendor-report@example.test',
            'password' => Hash::make('password'),
            'branch_id' => 1,
            'phone_number' => '555',
            'profile_pic' => '',
            'role_name' => 'admin',
            'status' => 1,
            'subscriber_id' => 1,
        ]);
    }

    private function seedVendorData(): Company
    {
        Branch::create([
            'id' => 1,
            'branch_name' => 'Main',
            'status' => 1,
        ]);

        CompanyInfo::create([
            'name_ar' => 'شركة اختبار',
            'address' => 'عنوان',
        ]);

        $vendor = Company::create([
            'group_id' => 4,
            'name' => 'Vendor One',
            'vat_no' => '123',
            'state' => 'Riyadh',
            'status' => 1,
        ]);

        VendorMovement::create([
            'vendor_id' => $vendor->id,
            'paid' => 0,
            'debit' => 0,
            'credit' => 150,
            'date' => '2026-01-10',
            'invoice_type' => 'Purchases',
            'invoice_id' => 1,
            'invoice_no' => 'P-1',
            'paid_by' => 'cash',
            'branch_id' => 1,
        ]);

        return $vendor;
    }

    public function test_vendor_balance_pdf_route_returns_pdf()
    {
        if (! file_exists(config('snappy.pdf.binary'))) {
            $this->markTestSkipped('wkhtmltopdf is not installed.');
        }

        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class,
            \App\Http\Middleware\PersistUserLocale::class,
        ]);

        $user = $this->createUser();
        $vendor = $this->seedVendorData();

        $response = $this->actingAs($user, 'admin-web')
            ->get(route('reports.vendors_balance_pdf', [
            'company_id' => $vendor->id,
            'date_from' => '2026-01-01',
            'date_to' => '2026-01-31',
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_vendor_balance_view_is_rtl_ready()
    {
        $blade = file_get_contents(resource_path('views/reports/vendor-balance.blade.php'));

        $this->assertStringContainsString('dir="rtl"', $blade);
        $this->assertStringContainsString('lang="ar"', $blade);
    }

    public function test_pdf_viewer_route_is_available()
    {
        $response = $this->get('/pdfjs/web/viewer.html');

        $response->assertOk();
        $this->assertStringContainsString('text/html', $response->headers->get('content-type'));
    }
}
