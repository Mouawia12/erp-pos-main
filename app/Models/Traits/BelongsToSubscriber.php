<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait BelongsToSubscriber
{
    protected static function bootBelongsToSubscriber()
    {
        static::creating(function ($model) {
            $user = Auth::user();
            if (
                $user
                && $user->subscriber_id
                && Schema::hasColumn($model->getTable(), 'subscriber_id')
            ) {
                $model->subscriber_id = $model->subscriber_id ?? $user->subscriber_id;
            }
        });

        static::addGlobalScope('subscriber', function (Builder $builder) {
            $user = Auth::user();
            $model = $builder->getModel();
            if (
                $user
                && $user->subscriber_id
                && Schema::hasColumn($model->getTable(), 'subscriber_id')
            ) {
                $builder->where($builder->getModel()->getTable() . '.subscriber_id', $user->subscriber_id);
            }
        });
    }
}
