<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDocumentCounter extends Model
{
    protected $fillable = [
        'user_id',
        'doc_type',
        'branch_id',
        'next_number',
        'prefix',
    ];
}
