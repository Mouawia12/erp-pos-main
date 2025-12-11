<?php

namespace App\Services\ZatcaIntegration;

use App\Models\Branch;
use App\Models\BranchZatcaSetting;
use App\Services\Zatca\OnBoarding;
use Illuminate\Support\Str;
use RuntimeException;

class ZatcaOnboardingService
{
    public function authorize(Branch $branch, string $otp, array $overrides = [], bool $forceSimulation = false): BranchZatcaSetting
    {
        $config = config('zatca');

        $setting = $branch->zatcaSetting ?: new BranchZatcaSetting(['branch_id' => $branch->id]);
        $stage = $overrides['env'] ?? $setting->zatca_stage ?? ($config['env'] ?? 'developer-portal');
        $invoiceType = $overrides['invoice_type'] ?? $setting->invoice_type ?? ($config['onboarding_invoice_type'] ?? '1100');
        $businessCategory = $overrides['business_category'] ?? $setting->business_category ?? ($config['business_category'] ?? 'Professional Services');
        $egsSerial = $overrides['egs_serial'] ?? $setting->egs_serial_number ?? $this->generateDefaultEgsSerial($branch);

        $organizationName = $config['organization_name'] ?? $branch->branch_name ?? config('app.name');
        $organizationUnit = $config['organization_unit'] ?? $branch->branch_name ?? 'HQ';
        $registeredAddress = $config['registered_address'] ?? $branch->branch_address ?? 'N/A';
        $email = $branch->contact_email ?? $config['email'] ?? config('mail.from.address') ?? 'support@example.com';
        $countryCode = data_get($config, 'supplier.country', 'SA');
        $vatNumber = $branch->tax_number ?? $config['vat_number'];

        if (empty($vatNumber)) {
            throw new RuntimeException('Branch VAT/TIN number is required before onboarding with ZATCA.');
        }

        $simulate = $forceSimulation || (bool) ($config['local_simulation'] ?? false);
        $simulationContext = array_merge($overrides, [
            'env' => $stage,
            'organization_name' => $organizationName,
            'organization_unit' => $organizationUnit,
            'registered_address' => $registeredAddress,
            'egs_serial' => $egsSerial,
        ]);

        if ($simulate) {
            $response = $this->simulateAuthorization($otp, $simulationContext, $config);
        } else {
            $response = (new OnBoarding())
                ->setZatcaEnv($stage)
                ->setZatcaLang($config['language'] ?? 'en')
                ->setEmailAddress($email)
                ->setCommonName($organizationName)
                ->setCountryCode($countryCode)
                ->setOrganizationUnitName($organizationUnit)
                ->setOrganizationName($organizationName)
                ->setEgsSerialNumber($egsSerial)
                ->setVatNumber($vatNumber)
                ->setInvoiceType($invoiceType)
                ->setRegisteredAddress($registeredAddress)
                ->setAuthOtp($otp)
                ->setBusinessCategory($businessCategory)
                ->getAuthorization();
        }

        $setting->fill([
            'zatca_stage' => $stage,
            'invoice_type' => $invoiceType,
            'business_category' => $businessCategory,
            'egs_serial_number' => $egsSerial,
            'is_simulation' => $simulate,
            'requested_at' => now(),
            'last_payload' => $response,
        ]);

        if (($response['success'] ?? false) && ! empty($response['data'])) {
            $setting->fill($this->mapResponseData($response['data']));
        }

        $setting->save();

        if (! ($response['success'] ?? false)) {
            throw new RuntimeException($response['message'] ?? 'ZATCA onboarding request failed.');
        }

        return $setting->fresh('branch');
    }

    protected function mapResponseData(array $data): array
    {
        return [
            'cnf' => $data['configData'] ?? null,
            'private_key' => $data['privateKey'] ?? null,
            'public_key' => $data['publicKey'] ?? null,
            'csr_request' => $data['csrKey'] ?? null,
            'certificate' => $data['complianceCertificate'] ?? null,
            'secret' => $data['complianceSecret'] ?? null,
            'csid' => $data['complianceRequestID'] ?? null,
            'production_certificate' => $data['productionCertificate'] ?? null,
            'production_secret' => $data['productionCertificateSecret'] ?? null,
            'production_csid' => $data['productionCertificateRequestID'] ?? null,
        ];
    }

    protected function simulateAuthorization(string $otp, array $overrides, array $config): array
    {
        $reference = 'SIM-'.Str::upper(Str::random(8));
        $env = $overrides['env'] ?? $config['env'] ?? 'developer-portal';

        $certificateBundle = $this->generateSimulationCertificate($overrides, $config);

        $fakeValue = fn (string $label) => base64_encode($label.'-'.$reference.'-'.Str::uuid());

        $data = [
            'complianceCertificate' => $certificateBundle['certificate'],
            'complianceSecret' => $fakeValue('compliance-secret'),
            'complianceRequestID' => $reference,
            'productionCertificate' => $certificateBundle['certificate'],
            'productionCertificateSecret' => $fakeValue('production-secret'),
            'productionCertificateRequestID' => $reference.'-PROD',
            'privateKey' => $certificateBundle['private_key'],
            'publicKey' => $certificateBundle['public_key'],
            'csrKey' => $certificateBundle['csr'],
            'configData' => $fakeValue('config'),
            'requestedOtp' => $otp,
            'environment' => $env,
        ];

        return [
            'success' => true,
            'message' => 'Local simulation completed successfully.',
            'data' => $data,
            'simulation' => true,
        ];
    }

    protected function generateSimulationCertificate(array $overrides, array $config): array
    {
        $dn = [
            'countryName' => data_get($config, 'supplier.country', 'SA'),
            'stateOrProvinceName' => 'Simulation',
            'localityName' => 'Sandbox',
            'organizationName' => $overrides['organization_name'] ?? ($config['organization_name'] ?? 'Simulated Org'),
            'organizationalUnitName' => $overrides['organization_unit'] ?? ($config['organization_unit'] ?? 'IT'),
            'commonName' => $overrides['egs_serial'] ?? ($config['egs_serial_number'] ?? 'SIM-EGS'),
            'emailAddress' => $config['email'] ?? 'simulation@example.com',
        ];

        $keyResource = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        openssl_pkey_export($keyResource, $privateKeyPem);

        $csr = openssl_csr_new($dn, $keyResource, ['digest_alg' => 'sha256']);
        $certificateResource = openssl_csr_sign($csr, null, $keyResource, 365, ['digest_alg' => 'sha256']);
        openssl_x509_export($certificateResource, $certificatePem);

        $csrPem = '';
        openssl_csr_export($csr, $csrPem);
        $keyDetails = openssl_pkey_get_details($keyResource);
        $publicKeyPem = $keyDetails['key'] ?? '';

        return [
            'certificate' => base64_encode($certificatePem),
            'private_key' => base64_encode($privateKeyPem),
            'public_key' => base64_encode($publicKeyPem),
            'csr' => base64_encode($csrPem),
        ];
    }

    protected function generateDefaultEgsSerial(Branch $branch): string
    {
        return sprintf('BR-%d-%s', $branch->id, Str::upper(Str::random(6)));
    }
}
