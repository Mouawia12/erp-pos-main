<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSubscriber;

class AccountsTree extends Model
{
    use HasFactory, BelongsToSubscriber;
    protected $fillable = [
        'code',
        'name',
        'type',
        'parent_id',
        'parent_code',
        'level',
        'list',
        'department',
        'side',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public static function buildTree($accounts, int $rootParentId = 0)
    {
        $grouped = $accounts->groupBy(function ($account) {
            return $account->parent_id ?? 0;
        });

        $build = function ($parentId) use (&$build, $grouped) {
            return $grouped->get($parentId, collect())->map(function ($account) use (&$build) {
                $account->setRelation('children', $build($account->id));
                return $account;
            });
        };

        return $build($rootParentId);
    }
}
