<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Classes;
use App\Models\ClassLearner;
use App\Models\Grade;
use App\Models\HomeroomNote;
use App\Models\Learner;
use App\Models\LearnerExtracurricular;
use App\Models\Subject;
use App\Models\SubjectGroup;
use App\Models\Tutor;
use App\Models\User;
use App\Services\DTOs\ImportResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ImportService
{
    public function __construct(
        protected Tutor $tutor,
        protected Learner $learner,
        protected Grade $grade,
    ) {}

    private function notifyAdmins(string $type, int $imported, int $updated): void
    {
        $admins = User::role('admin')->get();
        $message = "Import {$type} selesai: {$imported} baru, {$updated} diperbarui.";

        foreach ($admins as $admin) {
            app(NotificationService::class)->createNotification(
                'info',
                $admin,
                ['message' => $message],
            );
        }
    }

    public function importTutors(array $data): ImportResult
    {
        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            $rowErrors = $this->validateRow($row, 'tutor');

            if (! empty($rowErrors)) {
                $skipped++;
                $errors[] = ['row' => $index + 1, 'errors' => $rowErrors];

                continue;
            }

            $tutor = $this->tutor
                ->where('email', $row['email'])
                ->first();

            if ($tutor) {
                $updateData = collect($row)->except('password')->toArray();
                $updateData['birth_date'] = ! empty($updateData['birth_date']) ? $updateData['birth_date'] : null;
                $tutor->update($updateData);
                $tutor->user->update([
                    'name' => $row['name'],
                    'email' => $row['email'],
                ]);
                $updated++;
            } else {
                $user = User::firstOrCreate(
                    ['email' => $row['email']],
                    [
                        'name' => $row['name'],
                        'password' => Hash::make($row['password'] ?? 'password123'),
                    ],
                );

                if (! $user->hasRole('tutor')) {
                    $user->assignRole('tutor');
                }

                $this->tutor->create([
                    'user_id' => $user->id,
                    'nip' => $row['nip'],
                    'name' => $row['name'],
                    'gender' => $row['gender'] ?? 'L',
                    'birth_place' => $row['birth_place'] ?? '',
                    'birth_date' => ! empty($row['birth_date']) ? $row['birth_date'] : null,
                    'address' => $row['address'] ?? '',
                    'phone' => $row['phone'] ?? '',
                    'email' => $row['email'],
                    'is_active' => $row['is_active'] ?? true,
                ]);
                $imported++;
            }
        }

        app(AuditService::class)->logImport('tutor', $imported, $updated, $skipped, $errors);
        $this->notifyAdmins('tutor', $imported, $updated);

        return new ImportResult(
            success: empty($errors),
            imported: $imported,
            updated: $updated,
            skipped: $skipped,
            errors: $errors,
        );
    }

    public function importLearners(array $data): ImportResult
    {
        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        $activeAcademicYear = AcademicYear::where('is_active', true)->first();

        foreach ($data as $index => $row) {
            $rowErrors = $this->validateRow($row, 'learner');

            if (! empty($rowErrors)) {
                $skipped++;
                $errors[] = ['row' => $index + 1, 'errors' => $rowErrors];

                continue;
            }

            $class = null;
            if (! empty($row['class_name'])) {
                $class = Classes::where('name', $row['class_name'])->first();

                if (! $class) {
                    $skipped++;
                    $errors[] = ['row' => $index + 1, 'errors' => ['class_name' => ['Kelas "'.$row['class_name'].'" tidak ditemukan.']]];

                    continue;
                }
            }

            $learnerData = collect($row)->except(['class_name'])->toArray();

            foreach (['birth_date', 'admission_date'] as $dateField) {
                if (empty($learnerData[$dateField])) {
                    $learnerData[$dateField] = null;
                }
            }

            foreach (['child_order'] as $intField) {
                if (isset($learnerData[$intField]) && ($learnerData[$intField] === '' || $learnerData[$intField] === '-')) {
                    $learnerData[$intField] = null;
                }
            }

            if ($class) {
                $learnerData['program_id'] = $class->program_id;
            }

            $learner = $this->learner->where('nis', $row['nis'])->first();

            if (! $learner && ! empty($row['nisn'])) {
                $learner = $this->learner->where('nisn', $row['nisn'])->first();
            }

            if ($learner) {
                $learner->update($learnerData);
                $updated++;
            } else {
                $learner = $this->learner->create($learnerData);
                $imported++;
            }

            if ($class && $activeAcademicYear) {
                ClassLearner::updateOrCreate(
                    [
                        'learner_id' => $learner->id,
                        'class_id' => $class->id,
                        'academic_year_id' => $activeAcademicYear->id,
                    ],
                    [],
                );
            }
        }

        app(AuditService::class)->logImport('learner', $imported, $updated, $skipped, $errors);
        $this->notifyAdmins('warga belajar', $imported, $updated);

        return new ImportResult(
            success: empty($errors),
            imported: $imported,
            updated: $updated,
            skipped: $skipped,
            errors: $errors,
        );
    }

    public function importGrades(array $data): ImportResult
    {
        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        $nisList = collect($data)->pluck('nis')->filter()->unique()->values()->all();
        $learners = Learner::whereIn('nis', $nisList)->get()->keyBy('nis');

        foreach ($data as $index => $row) {
            $row['learner_id'] ??= $learners->get($row['nis'] ?? '')?->id;

            $rowErrors = $this->validateRow($row, 'grade');

            if (! empty($rowErrors)) {
                $skipped++;
                $errors[] = ['row' => $index + 1, 'errors' => $rowErrors];

                continue;
            }

            $exists = $this->grade
                ->where('learner_id', $row['learner_id'])
                ->where('subject_id', $row['subject_id'])
                ->where('academic_year_id', $row['academic_year_id'])
                ->where('semester_id', $row['semester_id'])
                ->exists();

            $row['status'] = 'published';

            if ($exists) {
                $grade = $this->grade
                    ->where('learner_id', $row['learner_id'])
                    ->where('subject_id', $row['subject_id'])
                    ->where('academic_year_id', $row['academic_year_id'])
                    ->where('semester_id', $row['semester_id'])
                    ->first();

                $updateData = [
                    'task_score' => $row['task_score'] === '' || $row['task_score'] === null ? 0 : $row['task_score'],
                    'pts_score' => $row['pts_score'] === '' || $row['pts_score'] === null ? 0 : $row['pts_score'],
                    'pas_score' => $row['pas_score'] === '' || $row['pas_score'] === null ? 0 : $row['pas_score'],
                    'practice_score' => $row['practice_score'] === '' || $row['practice_score'] === null ? 0 : $row['practice_score'],
                    'status' => $row['status'],
                ];

                if (isset($row['competency_description'])) {
                    $updateData['competency_description'] = $row['competency_description'];
                }

                $grade->update($updateData);

                $service = app(GradeService::class);
                $service->calculateAndSave($grade);

                $updated++;
            } else {
                $grade = $this->grade->create($row);

                $service = app(GradeService::class);
                $service->calculateAndSave($grade);

                $imported++;
            }
        }

        app(AuditService::class)->logImport('grade', $imported, $updated, $skipped, $errors);
        $this->notifyAdmins('nilai', $imported, $updated);

        return new ImportResult(
            success: empty($errors),
            imported: $imported,
            updated: $updated,
            skipped: $skipped,
            errors: $errors,
        );
    }

    public function importAttendances(array $data): ImportResult
    {
        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        $nisList = collect($data)->pluck('nis')->filter()->unique()->values()->all();
        $learners = Learner::whereIn('nis', $nisList)->get()->keyBy('nis');

        foreach ($data as $index => $row) {
            $row['learner_id'] ??= $learners->get($row['nis'] ?? '')?->id;

            $rowErrors = $this->validateRow($row, 'attendance');

            if (! empty($rowErrors)) {
                $skipped++;
                $errors[] = ['row' => $index + 1, 'errors' => $rowErrors];

                continue;
            }

            $toInt = fn ($v) => $v === '' || $v === null ? 0 : (int) $v;

            $exists = Attendance::where('learner_id', $row['learner_id'])
                ->where('academic_year_id', $row['academic_year_id'])
                ->where('semester_id', $row['semester_id'])
                ->exists();

            Attendance::updateOrCreate(
                [
                    'learner_id' => $row['learner_id'],
                    'academic_year_id' => $row['academic_year_id'],
                    'semester_id' => $row['semester_id'],
                ],
                [
                    'sick' => $toInt($row['sick'] ?? 0),
                    'permission' => $toInt($row['permission'] ?? 0),
                    'absent' => $toInt($row['absent'] ?? 0),
                ],
            );

            if ($exists) {
                $updated++;
            } else {
                $imported++;
            }
        }

        app(AuditService::class)->logImport('attendance', $imported, $updated, $skipped, $errors);
        $this->notifyAdmins('presensi', $imported, $updated);

        return new ImportResult(
            success: empty($errors),
            imported: $imported,
            updated: $updated,
            skipped: $skipped,
            errors: $errors,
        );
    }

    public function importHomeroomNotes(array $data): ImportResult
    {
        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        $nisList = collect($data)->pluck('nis')->filter()->unique()->values()->all();
        $learners = Learner::whereIn('nis', $nisList)->get()->keyBy('nis');

        foreach ($data as $index => $row) {
            $row['learner_id'] ??= $learners->get($row['nis'] ?? '')?->id;

            $rowErrors = $this->validateRow($row, 'homeroom-note');

            if (! empty($rowErrors)) {
                $skipped++;
                $errors[] = ['row' => $index + 1, 'errors' => $rowErrors];

                continue;
            }

            $exists = HomeroomNote::where('learner_id', $row['learner_id'])
                ->where('academic_year_id', $row['academic_year_id'])
                ->where('semester_id', $row['semester_id'])
                ->exists();

            HomeroomNote::updateOrCreate(
                [
                    'learner_id' => $row['learner_id'],
                    'academic_year_id' => $row['academic_year_id'],
                    'semester_id' => $row['semester_id'],
                ],
                [
                    'note' => $row['note'] ?? '',
                ],
            );

            if ($exists) {
                $updated++;
            } else {
                $imported++;
            }
        }

        app(AuditService::class)->logImport('homeroom-note', $imported, $updated, $skipped, $errors);
        $this->notifyAdmins('catatan wali kelas', $imported, $updated);

        return new ImportResult(
            success: empty($errors),
            imported: $imported,
            updated: $updated,
            skipped: $skipped,
            errors: $errors,
        );
    }

    public function importExtracurriculars(array $data): ImportResult
    {
        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        $nisList = collect($data)->pluck('nis')->filter()->unique()->values()->all();
        $learners = Learner::whereIn('nis', $nisList)->get()->keyBy('nis');

        foreach ($data as $index => $row) {
            $row['learner_id'] ??= $learners->get($row['nis'] ?? '')?->id;

            if (empty($row['learner_id'])) {
                $skipped++;
                $errors[] = ['row' => $index + 1, 'errors' => ['nis' => ["Peserta didik dengan NIS '{$row['nis']}' tidak ditemukan."]]];

                continue;
            }

            $rowErrors = $this->validateRow($row, 'extracurricular');

            if (! empty($rowErrors)) {
                $skipped++;
                $errors[] = ['row' => $index + 1, 'errors' => $rowErrors];

                continue;
            }

            $exists = LearnerExtracurricular::where('learner_id', $row['learner_id'])
                ->where('extracurricular_id', $row['extracurricular_id'])
                ->where('academic_year_id', $row['academic_year_id'])
                ->where('semester_id', $row['semester_id'])
                ->exists();

            LearnerExtracurricular::updateOrCreate(
                [
                    'learner_id' => $row['learner_id'],
                    'extracurricular_id' => $row['extracurricular_id'],
                    'academic_year_id' => $row['academic_year_id'],
                    'semester_id' => $row['semester_id'],
                ],
                [
                    'predicate' => $row['predicate'] ?? $row['grade'] ?? '',
                    'description' => $row['description'] ?? $row['notes'] ?? '',
                ],
            );

            if ($exists) {
                $updated++;
            } else {
                $imported++;
            }
        }

        app(AuditService::class)->logImport('extracurricular', $imported, $updated, $skipped, $errors);
        $this->notifyAdmins('ekstrakurikuler', $imported, $updated);

        return new ImportResult(
            success: empty($errors),
            imported: $imported,
            updated: $updated,
            skipped: $skipped,
            errors: $errors,
        );
    }

    public function importSubjects(array $data): ImportResult
    {
        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        $subjectGroups = SubjectGroup::pluck('id', 'name');

        foreach ($data as $index => $row) {
            $rowErrors = $this->validateRow($row, 'subject');

            if (! empty($rowErrors)) {
                $skipped++;
                $errors[] = ['row' => $index + 1, 'errors' => $rowErrors];

                continue;
            }

            $subjectGroupId = null;
            $subjectGroupName = $row['subject_group_name'] ?? '';
            if (! empty($subjectGroupName)) {
                $subjectGroupId = $subjectGroups[$subjectGroupName] ?? null;
                if (! $subjectGroupId) {
                    $errors[] = ['row' => $index + 1, 'errors' => ['subject_group' => ["Kelompok '{$subjectGroupName}' tidak ditemukan."]]];
                    $skipped++;

                    continue;
                }
            }

            $subject = Subject::where('code', $row['code'])->first();

            if (! $subject && ! empty($row['class_id']) && ! empty($row['name'])) {
                $subject = Subject::where('class_id', $row['class_id'])
                    ->where('name', $row['name'])
                    ->first();
            }

            if ($subject) {
                $subject->update([
                    'name' => $row['name'],
                    'class_id' => $row['class_id'] ?? $subject->class_id,
                    'subject_group_id' => $subjectGroupId ?? $subject->subject_group_id,
                    'description' => $row['description'] ?? $subject->description,
                    'is_active' => $row['is_active'] ?? $subject->is_active,
                ]);
                $updated++;
            } else {
                Subject::create([
                    'code' => $row['code'],
                    'name' => $row['name'],
                    'class_id' => $row['class_id'] ?? null,
                    'subject_group_id' => $subjectGroupId,
                    'description' => $row['description'] ?? '',
                    'is_active' => $row['is_active'] ?? true,
                ]);
                $imported++;
            }
        }

        app(AuditService::class)->logImport('subject', $imported, $updated, $skipped, $errors);
        $this->notifyAdmins('mata pelajaran', $imported, $updated);

        return new ImportResult(
            success: empty($errors),
            imported: $imported,
            updated: $updated,
            skipped: $skipped,
            errors: $errors,
        );
    }

    public function validateRow(array $row, string $type): array
    {
        $errors = [];

        $rules = match ($type) {
            'tutor' => [
                'nip' => 'required|string',
                'name' => 'required|string',
                'email' => 'required|email',
                'password' => 'string',
            ],
            'learner' => [
                'nis' => 'required|string',
                'nisn' => 'string',
                'name' => 'required|string',
                'class_name' => 'string',
            ],
            'grade' => [
                'learner_id' => 'integer|exists:learners,id',
                'nis' => 'required|string',
                'subject_id' => 'required|integer|exists:subjects,id',
                'academic_year_id' => 'required|integer|exists:academic_years,id',
                'semester_id' => 'required|integer|exists:semesters,id',
            ],
            'attendance' => [
                'learner_id' => 'integer|exists:learners,id',
                'nis' => 'required|string',
                'academic_year_id' => 'required|integer|exists:academic_years,id',
                'semester_id' => 'required|integer|exists:semesters,id',
            ],
            'homeroom-note' => [
                'learner_id' => 'integer|exists:learners,id',
                'nis' => 'required|string',
                'academic_year_id' => 'required|integer|exists:academic_years,id',
                'semester_id' => 'required|integer|exists:semesters,id',
            ],
            'extracurricular' => [
                'learner_id' => 'integer|exists:learners,id',
                'nis' => 'required|string',
                'extracurricular_id' => 'required|integer|exists:extracurriculars,id',
                'academic_year_id' => 'required|integer|exists:academic_years,id',
                'semester_id' => 'required|integer|exists:semesters,id',
            ],
            'subject' => [
                'code' => 'required|string',
                'name' => 'required|string',
            ],
            default => [],
        };

        foreach ($rules as $field => $rule) {
            $segments = explode('|', $rule);

            if (in_array('required', $segments) && empty($row[$field] ?? '')) {
                $errors[$field][] = "{$field} is required.";
            }

            if (isset($row[$field]) && in_array('email', $segments)) {
                if (! filter_var($row[$field], FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = "{$field} must be a valid email.";
                }
            }

            if (isset($row[$field]) && in_array('integer', $segments)) {
                if (! filter_var($row[$field], FILTER_VALIDATE_INT)) {
                    $errors[$field][] = "{$field} must be an integer.";
                }
            }

            foreach ($segments as $segment) {
                if (str_starts_with($segment, 'exists:')) {
                    $parts = explode(',', substr($segment, 7));
                    $table = $parts[0] ?? null;
                    $column = $parts[1] ?? 'id';

                    if ($table && ! DB::table($table)->where($column, $row[$field] ?? null)->exists()) {
                        $errors[$field][] = "{$field} does not exist in {$table}.";
                    }
                }
            }
        }

        return $errors;
    }

    public function generateTemplate(string $type): array
    {
        return match ($type) {
            'tutor' => [
                'nip', 'name', 'gender', 'birth_place', 'birth_date',
                'address', 'phone', 'email', 'is_active',
            ],
            'learner' => [
                'nis', 'nisn', 'name', 'gender', 'birth_place',
                'birth_date', 'address', 'status', 'class_name',
                'religion', 'child_order', 'phone', 'admission_date',
                'admission_class', 'admission_status', 'father_name',
                'father_job', 'mother_name', 'mother_job', 'guardian_name',
                'guardian_job', 'report_number',
            ],
            'grade' => [
                'learner_id', 'subject_id', 'academic_year_id', 'semester_id',
                'task_score', 'pts_score', 'pas_score', 'practice_score',
            ],
            'subject' => [
                'name', 'subject_group', 'description', 'is_active',
            ],
            default => [],
        };
    }
}
