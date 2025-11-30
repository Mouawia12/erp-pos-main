<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployerCategory extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable=['code','name'];

    public function Employers(){
        return $this->hasMany(Employer::class);
    }
}
