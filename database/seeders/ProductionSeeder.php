<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([RolePermissionSeeder::class, MasterDataSeeder::class]);

        $adminRole = Role::where('name', 'admin')->first();
        $admin = User::firstOrCreate(['email' => 'admin@globalconsultancy.com'], [
            'name' => 'Administrator',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        if ($adminRole && ! $admin->role_id) {
            $admin->role_id = $adminRole->id;
            $admin->save();
        }
    }
}
