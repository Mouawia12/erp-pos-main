<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyInfo extends Model
{
    use HasFactory;
    protected $table = "company_infos";
    protected $fillable = [
        'name_ar',
        'name_en',
        'phone',
        'phone2',
        'fax',
        'email',
        'website',
        'taxNumber',
        'registrationNumber',
        'address',
        'currency_ar',
        'currency_en',
        'currency_label',
        'currency_label_en',
        'logo',
        'user_id'
    ];
}
