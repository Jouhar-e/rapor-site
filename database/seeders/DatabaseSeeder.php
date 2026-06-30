<?php

namespace Database\Seeders;

use App\Models\Tutor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'tutor']);

        if (User::where('email', 'admin@pkbm.test')->doesntExist()) {
            $admin = User::factory()->create([
                'name' => 'Admin PKBM',
                'email' => 'admin@pkbm.test',
            ]);
            $admin->assignRole('admin');
        }

        if (User::where('email', 'tutor@pkbm.test')->doesntExist()) {
            $tutorUser = User::factory()->create([
                'name' => 'Tutor PKBM',
                'email' => 'tutor@pkbm.test',
            ]);
            $tutorUser->assignRole('tutor');

            Tutor::factory()->create([
                'user_id' => $tutorUser->id,
                'name' => 'Tutor PKBM',
                'email' => 'tutor@pkbm.test',
            ]);
        }

        $this->call([
            PermissionSeeder::class,
            GradePredicateSeeder::class,
            PhaseSeeder::class,
            SubjectGroupSeeder::class,
        ]);

        if ($adminRole->hasAllPermissions(Permission::all())) {
            return;
        }

        $adminRole->syncPermissions(Permission::all());
    }
}
