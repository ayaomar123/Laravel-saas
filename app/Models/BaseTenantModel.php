<?php

namespace App\Models;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;

abstract class BaseTenantModel extends Model
{
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());
    }

    protected static function boot(): void
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!$model->tenant_id && app('tenant')) {
                $model->tenant_id = app('tenant')->id;
            }
        });
    }
}