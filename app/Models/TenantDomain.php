<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantDomain extends Model
{
    protected $fillable = ['tenant_id', 'domain', 'verified_at'];
    
    protected $dates = ['verified_at', 'created_at', 'updated_at'];
    
    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}