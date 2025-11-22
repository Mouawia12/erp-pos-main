<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToSubscriber
{
    protected static function bootBelongsToSubscriber()
    {
        static::creating(function ($model) {
            $user = Auth::user();
            if ($user && $user->subscriber_id) {
                $model->subscriber_id = $model->subscriber_id ?? $user->subscriber_id;
            }
        });

        static::addGlobalScope('subscriber', function (Builder $builder) {
            $user = Auth::user();
            if ($user && $user->subscriber_id) {
                $builder->where($builder->getModel()->getTable() . '.subscriber_id', $user->subscriber_id);
            }
        });
    }
}
