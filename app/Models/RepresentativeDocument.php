<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepresentativeDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'representative_id',
        'title',
        'document_type',
        'expiry_date',
        'file_path',
        'uploaded_by',
    ];

    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }
}
