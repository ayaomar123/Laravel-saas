<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends BaseTenantModel
{
    protected $fillable = ['tenant_id', 'title', 'completed'];
    
    protected $casts = [
        'completed' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}