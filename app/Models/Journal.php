<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSubscriber;
use App\Models\Traits\GuardsFiscalYear;

class Journal extends Model
{
    use HasFactory, BelongsToSubscriber, GuardsFiscalYear;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_POSTED = 'posted';
    public const STATUS_REVERSED = 'reversed';

    protected $fillable = [
        'branch_id',
        'date',
        'basedon_no',
        'basedon_id',
        'baseon_text',
        'total_credit',
        'total_debit',
        'notes',
        'status',
        'reversed_journal_id',
        'reverses_journal_id',
        'reversed_at',
    ];

}
