<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriberDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscriber_id','title','file_path','uploaded_by','archived_at'
    ];

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }

    protected $casts = [
        'archived_at' => 'datetime',
    ];
}
