<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'dashboard.view',

            'program.view', 'program.create', 'program.edit', 'program.delete',
            'academic-year.view', 'academic-year.create', 'academic-year.edit', 'academic-year.delete',
            'semester.view', 'semester.create', 'semester.edit', 'semester.delete',
            'tutor.view', 'tutor.create', 'tutor.edit', 'tutor.delete',
            'learner.view', 'learner.create', 'learner.edit', 'learner.delete',
            'class.view', 'class.create', 'class.edit', 'class.delete',
            'subject.view', 'subject.create', 'subject.edit', 'subject.delete',
            'extracurricular.view', 'extracurricular.create', 'extracurricular.edit', 'extracurricular.delete',
            'homeroom-teacher.view', 'homeroom-teacher.create', 'homeroom-teacher.edit', 'homeroom-teacher.delete',
            'placement.view', 'placement.create', 'placement.edit', 'placement.delete',
            'grade.view', 'grade.create', 'grade.edit', 'grade.delete', 'grade.publish', 'grade.lock',
            'attendance.view', 'attendance.create', 'attendance.edit', 'attendance.delete',
            'learner-extracurricular.view', 'learner-extracurricular.create', 'learner-extracurricular.edit', 'learner-extracurricular.delete',
            'homeroom-note.view', 'homeroom-note.create', 'homeroom-note.edit', 'homeroom-note.delete',
            'promotion-mapping.view', 'promotion-mapping.create', 'promotion-mapping.edit', 'promotion-mapping.delete',
            'promotion.process', 'promotion.view',
            'report.view',
            'import.tutor', 'import.learner', 'import.grade', 'import.attendance', 'import.homeroom-note', 'import.extracurricular', 'import.subject',
            'export.grade',
            'audit-log.view', 'backup.create', 'backup.restore', 'backup.view',
            'setting.view', 'setting.edit',

            'phase.view', 'phase.create', 'phase.edit', 'phase.delete',
            'subject-group.view', 'subject-group.create', 'subject-group.edit', 'subject-group.delete',
            'learner-report.view', 'learner-report.create', 'learner-report.edit', 'learner-report.delete',
            'school-profile.view', 'school-profile.edit',
            'grading-setting.view', 'grading-setting.edit',
            'notification.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $tutor = Role::findByName('tutor');
        $tutor->syncPermissions([
            'dashboard.view',
            'grade.view', 'grade.create', 'grade.edit',
            'attendance.view', 'attendance.create', 'attendance.edit',
            'learner-extracurricular.view', 'learner-extracurricular.create', 'learner-extracurricular.edit',
            'homeroom-note.view', 'homeroom-note.create', 'homeroom-note.edit',
            'import.tutor', 'import.learner', 'import.grade', 'import.attendance', 'import.homeroom-note', 'import.extracurricular', 'import.subject',
            'export.grade',
            'promotion.view',
            'learner-report.view',
        ]);
    }
}
