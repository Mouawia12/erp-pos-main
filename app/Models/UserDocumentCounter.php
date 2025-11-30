<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Model;

class UserDocumentCounter extends Model
{
    use BelongsToSubscriber;

    protected $fillable = [
        'user_id',
        'doc_type',
        'branch_id',
        'next_number',
        'prefix',
    ];
}
