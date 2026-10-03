<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['admin', 'manager', 'staff', 'candidate'];
        $roleModels = [];
        foreach ($roles as $roleName) {
            $roleModels[$roleName] = Role::firstOrCreate(
                ['name' => $roleName],
                ['guard_name' => 'web']
            );
        }

        $allPermissions = [
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'teams.view', 'teams.create', 'teams.edit', 'teams.delete',
            'candidates.view', 'candidates.create', 'candidates.edit', 'candidates.delete', 'candidates.import', 'candidates.export',
            'applications.view', 'applications.create', 'applications.edit', 'applications.delete', 'applications.status_change', 'applications.export',
            'documents.upload', 'documents.view', 'documents.verify', 'documents.reject', 'documents.download', 'documents.version',
            'universities.view', 'universities.create', 'universities.edit', 'universities.delete',
            'courses.view', 'courses.create', 'courses.edit', 'courses.delete',
            'offers.view', 'offers.create', 'offers.edit', 'offers.delete',
            'cas.view', 'cas.edit',
            'visa.view', 'visa.edit',
            'enrolments.view', 'enrolments.create', 'enrolments.edit', 'enrolment.view', 'enrolment.edit',
            'tasks.view', 'tasks.create', 'tasks.edit', 'tasks.delete',
            'appointments.view', 'appointments.create', 'appointments.edit', 'appointments.delete',
            'commissions.view', 'commissions.claim', 'commissions.edit',
            'invoices.view', 'invoices.generate', 'invoices.edit', 'invoices.delete',
            'payments.view', 'payments.record',
            'leads.view', 'leads.create', 'leads.edit', 'leads.delete', 'leads.convert',
            'reports.view', 'reports.export',
            'cms.view', 'cms.edit',
            'email_templates.view', 'email_templates.edit',
            'settings.view', 'settings.edit',
            'audit.logs.view',
        ];

        foreach ($allPermissions as $name) {
            Permission::firstOrCreate(['name' => $name], ['guard_name' => 'web']);
        }

        $adminRole = $roleModels['admin'];
        $adminRole->permissions()->sync(Permission::pluck('id')->all());

        $managerPermissions = [
            'users.view',
            'candidates.view', 'candidates.create', 'candidates.edit', 'candidates.export',
            'applications.view', 'applications.create', 'applications.edit', 'applications.status_change',
            'documents.upload', 'documents.view', 'documents.verify', 'documents.reject', 'documents.download',
            'universities.view', 'courses.view',
            'offers.view', 'offers.create', 'offers.edit',
            'cas.view', 'cas.edit',
            'visa.view', 'visa.edit',
            'enrolments.view', 'enrolments.edit', 'enrolment.view', 'enrolment.edit',
            'tasks.view', 'tasks.create', 'tasks.edit',
            'appointments.view', 'appointments.create', 'appointments.edit',
            'commissions.view', 'commissions.claim',
            'invoices.view', 'invoices.generate',
            'payments.view',
            'leads.view', 'leads.create', 'leads.edit', 'leads.convert',
            'reports.view',
            'cms.view', 'cms.edit',
            'settings.view', 'settings.edit',
        ];
        $managerRole = $roleModels['manager'];
        $managerRole->permissions()->sync(Permission::whereIn('name', $managerPermissions)->pluck('id')->all());

        $staffPermissions = [
            'candidates.view',
            'applications.view',
            'documents.upload', 'documents.view',
            'universities.view', 'courses.view',
            'offers.view',
            'cas.view',
            'visa.view',
            'enrolments.view', 'enrolment.view',
            'tasks.view', 'tasks.create', 'tasks.edit',
            'appointments.view', 'appointments.create',
            'commissions.view',
            'invoices.view',
            'payments.view',
            'leads.view', 'leads.create',
        ];
        $staffRole = $roleModels['staff'];
        $staffRole->permissions()->sync(Permission::whereIn('name', $staffPermissions)->pluck('id')->all());

        $candidatePermissions = ['candidates.view', 'documents.upload', 'documents.view'];
        $candidateRole = $roleModels['candidate'];
        $candidateRole->permissions()->sync(Permission::whereIn('name', $candidatePermissions)->pluck('id')->all());

        $adminUser = User::first();
        if ($adminUser && ! $adminUser->role_id) {
            $adminUser->role_id = $adminRole->id;
            $adminUser->save();
        }
    }
}
