<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubLedgerEntry extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = [
        'sub_ledger_id',
        'journal_id',
        'date',
        'debit',
        'credit',
        'notes',
        'branch_id',
        'subscriber_id',
    ];
}
