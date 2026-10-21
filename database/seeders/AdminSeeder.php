<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('GHOSTORCHID_ADMIN_PASSWORD');

        if (empty($password)) {
            throw new RuntimeException(
                'Please set GHOSTORCHID_ADMIN_PASSWORD in your .env file.'
            );
        }

        Admin::updateOrCreate(
            ['username' => 'blackorchidadmin'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make($password),
                'role' => 'superadmin',
                'status' => 'active',
            ]
        );

        $this->command?->info(
            'GhostOrchid administrator account updated successfully.'
        );
    }
}
