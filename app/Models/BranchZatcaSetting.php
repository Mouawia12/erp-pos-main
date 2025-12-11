<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchZatcaSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'zatca_stage',
        'invoice_type',
        'business_category',
        'egs_serial_number',
        'is_simulation',
        'cnf',
        'private_key',
        'public_key',
        'csr_request',
        'certificate',
        'secret',
        'csid',
        'production_certificate',
        'production_secret',
        'production_csid',
        'requested_at',
        'last_payload',
    ];

    protected $casts = [
        'is_simulation' => 'boolean',
        'requested_at' => 'datetime',
        'last_payload' => 'array',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function getCertificateBundle(bool $production = false): ?array
    {
        $certificate = $production ? $this->production_certificate : $this->certificate;
        $secret = $production ? $this->production_secret : $this->secret;
        $privateKey = $this->private_key;

        if (empty($certificate) || empty($secret) || empty($privateKey)) {
            return null;
        }

        return [
            'encoded' => $certificate,
            'secret' => $secret,
            'private_key' => $privateKey,
        ];
    }

    public function usesProductionStage(): bool
    {
        return ($this->zatca_stage && $this->zatca_stage !== 'developer-portal');
    }
}
