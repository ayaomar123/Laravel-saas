<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    protected $fillable = ['name'];
    
    public $timestamps = true;

    public function domains(): HasMany
    {
        return $this->hasMany(TenantDomain::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}