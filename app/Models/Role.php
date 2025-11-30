<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected static function booted(): void
    {
        static::creating(function (Role $role) {
            $user = Auth::user();
            if ($user && $user->subscriber_id && ! $role->subscriber_id) {
                $role->subscriber_id = $user->subscriber_id;
            }
        });

        static::addGlobalScope('subscriber', function (Builder $builder) {
            $user = Auth::user();
            if ($user && $user->subscriber_id) {
                $builder->where(function (Builder $query) use ($user) {
                    $query->whereNull('subscriber_id')
                        ->orWhere('subscriber_id', $user->subscriber_id);
                });
            }
        });
    }
}
