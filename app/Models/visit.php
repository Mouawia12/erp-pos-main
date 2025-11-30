<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class visit extends Model
{
    use HasFactory, BelongsToSubscriber;
    public $fillable = [
      'id',
      'rep_id',
      'client_id',
      'type',
      'date',
       'state',
       'notes'
    ];
}
