<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriberRenewal extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = [
        'subscriber_id','previous_end_date','new_end_date','added_days','added_months','added_years','notes','renewed_by'
    ];

    protected $casts = [
        'previous_end_date' => 'date',
        'new_end_date' => 'date',
    ];

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }
}
