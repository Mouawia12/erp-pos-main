<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSubscriber;

class AccountsTree extends Model
{
    use HasFactory, BelongsToSubscriber;
    protected $fillable = ['code','name','type','parent_id','parent_code','level','list','department','side'];
}
