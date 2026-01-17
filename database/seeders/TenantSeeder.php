<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\TenantDomain;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        // Create ACME tenant
        $acmeTenant = Tenant::create([
            'name' => 'ACME Corporation',
        ]);

        TenantDomain::create([
            'tenant_id' => $acmeTenant->id,
            'domain' => 'acme.test',
            'verified_at' => now(),
        ]);

        // Create Beta tenant
        $betaTenant = Tenant::create([
            'name' => 'Beta Company',
        ]);

        TenantDomain::create([
            'tenant_id' => $betaTenant->id,
            'domain' => 'beta.test',
            'verified_at' => now(),
        ]);
    }
}