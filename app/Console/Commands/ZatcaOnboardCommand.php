<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Services\ZatcaIntegration\ZatcaOnboardingService;
use Illuminate\Console\Command;

class ZatcaOnboardCommand extends Command
{
    protected $signature = 'zatca:onboard
        {otp : One-time password received from Fatoora/ZATCA}
        {--env= : Target environment developer-portal|simulation|core}
        {--invoice-type= : Invoice profile (1100/0100/1000)}
        {--egs= : Override EGS serial number}
        {--business-category= : Override business category text}
        {--branch= : Branch ID that will own the generated certificates}
        {--simulate : Run onboarding in local simulation mode without calling ZATCA}';

    protected $description = 'Submit the onboarding request to ZATCA and store the response locally.';

    public function handle(ZatcaOnboardingService $service): int
    {
        if (! config('zatca.enabled')) {
            $this->error('ZATCA integration is disabled. Set ZATCA_ENABLED=true in the environment file first.');
            return self::FAILURE;
        }

        $overrides = array_filter([
            'env' => $this->option('env'),
            'invoice_type' => $this->option('invoice-type'),
            'egs_serial' => $this->option('egs'),
            'business_category' => $this->option('business-category'),
        ], fn ($value) => ! is_null($value) && $value !== '');

        $branchId = (int) $this->option('branch');
        if (! $branchId) {
            $this->error('Please provide a branch id using --branch=<id>.');
            return self::FAILURE;
        }

        $branch = Branch::find($branchId);
        if (! $branch) {
            $this->error('Could not find a branch with id '.$branchId);
            return self::FAILURE;
        }

        $simulate = (bool) $this->option('simulate');

        if ($simulate) {
            $this->info('Running onboarding in local simulation mode (no real ZATCA request will be sent).');
        } elseif (config('zatca.local_simulation')) {
            $this->info('Local simulation mode is enabled through configuration.');
            $simulate = true;
        }

        try {
            $setting = $service->authorize($branch, $this->argument('otp'), $overrides, $simulate);
        } catch (\Throwable $exception) {
            $this->error('Failed to submit onboarding request: '.$exception->getMessage());
            return self::FAILURE;
        }

        $this->info(sprintf(
            'Onboarding stored for branch #%d (%s) in %s.',
            $branch->id,
            $branch->branch_name ?? 'Unnamed branch',
            $setting->zatca_stage
        ));
        $this->line(json_encode($setting->last_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return self::SUCCESS;
    }
}
