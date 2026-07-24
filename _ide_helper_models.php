<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property bool $is_active
 * @property bool $is_archived
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassLearner> $classLearners
 * @property-read int|null $class_learners_count
 * @property-read string $status
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Grade> $grades
 * @property-read int|null $grades_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HomeroomTeacher> $homeroomTeachers
 * @property-read int|null $homeroom_teachers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Semester> $semesters
 * @property-read int|null $semesters_count
 * @method static \Database\Factories\AcademicYearFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicYear newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicYear newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicYear query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicYear whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicYear whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicYear whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicYear whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicYear whereIsArchived($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicYear whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicYear whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicYear whereUpdatedAt($value)
 */
	class AcademicYear extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $learner_id
 * @property int $academic_year_id
 * @property int $semester_id
 * @property int $sick
 * @property int $permission
 * @property int $absent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademicYear $academicYear
 * @property-read \App\Models\Learner $learner
 * @property-read \App\Models\Semester $semester
 * @method static \Database\Factories\AttendanceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereAbsent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereAcademicYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereLearnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance wherePermission($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereSemesterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereSick($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereUpdatedAt($value)
 */
	class Attendance extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $action
 * @property string|null $model_type
 * @property int|null $model_id
 * @property array<array-key, mixed>|null $old_values
 * @property array<array-key, mixed>|null $new_values
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $model_basename
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\AuditLogFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereNewValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereOldValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUserId($value)
 */
	class AuditLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $filename
 * @property int|null $file_size
 * @property string|null $type
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $status
 * @property string|null $started_at
 * @property string|null $completed_at
 * @property string|null $notes
 * @method static \Database\Factories\BackupHistoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BackupHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BackupHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BackupHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BackupHistory whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BackupHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BackupHistory whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BackupHistory whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BackupHistory whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BackupHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BackupHistory whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BackupHistory whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BackupHistory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BackupHistory whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BackupHistory whereUpdatedAt($value)
 */
	class BackupHistory extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $learner_id
 * @property int $class_id
 * @property int $academic_year_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $semester_id
 * @property-read \App\Models\AcademicYear $academicYear
 * @property-read \App\Models\Classes $classes
 * @property-read \App\Models\Learner $learner
 * @property-read \App\Models\Semester|null $semester
 * @method static \Database\Factories\ClassLearnerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassLearner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassLearner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassLearner query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassLearner whereAcademicYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassLearner whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassLearner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassLearner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassLearner whereLearnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassLearner whereSemesterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassLearner whereUpdatedAt($value)
 */
	class ClassLearner extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $program_id
 * @property int|null $phase_id
 * @property string $name
 * @property string|null $description
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassLearner> $classLearners
 * @property-read int|null $class_learners_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PromotionMapping> $destinationPromotionMappings
 * @property-read int|null $destination_promotion_mappings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HomeroomTeacher> $homeroomTeachers
 * @property-read int|null $homeroom_teachers_count
 * @property-read \App\Models\Phase|null $phase
 * @property-read \App\Models\Program $program
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PromotionMapping> $sourcePromotionMappings
 * @property-read int|null $source_promotion_mappings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subject> $subjects
 * @property-read int|null $subjects_count
 * @method static \Database\Factories\ClassesFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classes query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classes whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classes whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classes wherePhaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classes whereProgramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classes whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Classes whereUpdatedAt($value)
 */
	class Classes extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $description
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LearnerExtracurricular> $learnerExtracurriculars
 * @property-read int|null $learner_extracurriculars_count
 * @method static \Database\Factories\ExtracurricularFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Extracurricular newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Extracurricular newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Extracurricular query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Extracurricular whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Extracurricular whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Extracurricular whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Extracurricular whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Extracurricular whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Extracurricular whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Extracurricular whereUpdatedAt($value)
 */
	class Extracurricular extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $learner_id
 * @property int $subject_id
 * @property int $academic_year_id
 * @property int $semester_id
 * @property numeric|null $task_score
 * @property numeric|null $pts_score
 * @property numeric|null $pas_score
 * @property numeric|null $practice_score
 * @property numeric|null $final_score
 * @property string|null $predicate
 * @property string|null $description
 * @property string|null $competency_description
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademicYear $academicYear
 * @property-read \App\Models\Learner $learner
 * @property-read \App\Models\Semester $semester
 * @property-read \App\Models\Subject $subject
 * @method static \Database\Factories\GradeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereAcademicYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereCompetencyDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereFinalScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereLearnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade wherePasScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade wherePracticeScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade wherePredicate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade wherePtsScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereSemesterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereTaskScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereUpdatedAt($value)
 */
	class Grade extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property numeric $min_score
 * @property numeric $max_score
 * @property string $predicate
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\GradePredicateFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradePredicate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradePredicate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradePredicate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradePredicate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradePredicate whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradePredicate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradePredicate whereMaxScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradePredicate whereMinScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradePredicate wherePredicate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradePredicate whereUpdatedAt($value)
 */
	class GradePredicate extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property numeric $task_percentage
 * @property numeric $pts_percentage
 * @property numeric $pas_percentage
 * @property numeric $practice_percentage
 * @property numeric $min_score
 * @property numeric $max_score
 * @property int $rounding_digits
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\GradingSettingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradingSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradingSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradingSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradingSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradingSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradingSetting whereMaxScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradingSetting whereMinScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradingSetting wherePasPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradingSetting wherePracticePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradingSetting wherePtsPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradingSetting whereRoundingDigits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradingSetting whereTaskPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GradingSetting whereUpdatedAt($value)
 */
	class GradingSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $learner_id
 * @property int $academic_year_id
 * @property int $semester_id
 * @property string $note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademicYear $academicYear
 * @property-read \App\Models\Learner $learner
 * @property-read \App\Models\Semester $semester
 * @method static \Database\Factories\HomeroomNoteFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomNote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomNote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomNote query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomNote whereAcademicYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomNote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomNote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomNote whereLearnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomNote whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomNote whereSemesterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomNote whereUpdatedAt($value)
 */
	class HomeroomNote extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property int $class_id
 * @property int $academic_year_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademicYear $academicYear
 * @property-read \App\Models\Classes $classes
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\HomeroomTeacherFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomTeacher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomTeacher newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomTeacher query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomTeacher whereAcademicYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomTeacher whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomTeacher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomTeacher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomTeacher whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HomeroomTeacher whereUserId($value)
 */
	class HomeroomTeacher extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $type
 * @property string $file_name
 * @property int $total_rows
 * @property int $imported
 * @property int $updated
 * @property int $skipped
 * @property array<array-key, mixed>|null $errors
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $creator
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImportHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImportHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImportHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImportHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImportHistory whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImportHistory whereErrors($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImportHistory whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImportHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImportHistory whereImported($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImportHistory whereSkipped($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImportHistory whereTotalRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImportHistory whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImportHistory whereUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImportHistory whereUpdatedAt($value)
 */
	class ImportHistory extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $program_id
 * @property string $nis
 * @property string|null $nisn
 * @property string $name
 * @property string $gender
 * @property string|null $religion
 * @property int|null $child_order
 * @property string|null $phone
 * @property \Illuminate\Support\Carbon|null $admission_date
 * @property string|null $admission_class
 * @property string|null $admission_status
 * @property string|null $father_name
 * @property string|null $father_job
 * @property string|null $mother_name
 * @property string|null $mother_job
 * @property string|null $guardian_name
 * @property string|null $guardian_job
 * @property string|null $report_number
 * @property string|null $birth_place
 * @property \Illuminate\Support\Carbon|null $birth_date
 * @property string|null $address
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $attendances
 * @property-read int|null $attendances_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassLearner> $classLearners
 * @property-read int|null $class_learners_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Grade> $grades
 * @property-read int|null $grades_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HomeroomNote> $homeroomNotes
 * @property-read int|null $homeroom_notes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LearnerExtracurricular> $learnerExtracurriculars
 * @property-read int|null $learner_extracurriculars_count
 * @property-read \App\Models\Program $program
 * @method static \Database\Factories\LearnerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Learner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Learner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Learner query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Learner whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Learner whereAdmissionClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Learner whereAdmissionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Learner whereAdmissionStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Learner whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Learner whereBirthPlace($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Learner whereChildOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Learner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Learner whereFatherJob($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Learner whereFatherName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Learner whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Learner whereGuardianJob($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Learner whereGuardianName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Learner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Learner whereMotherJob($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Learner whereMotherName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Learner whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Learner whereNis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Learner whereNisn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Learner wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Learner whereProgramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Learner whereReligion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Learner whereReportNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Learner whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Learner whereUpdatedAt($value)
 */
	class Learner extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $learner_id
 * @property int $extracurricular_id
 * @property int $academic_year_id
 * @property int $semester_id
 * @property string|null $predicate
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademicYear $academicYear
 * @property-read \App\Models\Extracurricular $extracurricular
 * @property-read \App\Models\Learner $learner
 * @property-read \App\Models\Semester $semester
 * @method static \Database\Factories\LearnerExtracurricularFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearnerExtracurricular newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearnerExtracurricular newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearnerExtracurricular query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearnerExtracurricular whereAcademicYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearnerExtracurricular whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearnerExtracurricular whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearnerExtracurricular whereExtracurricularId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearnerExtracurricular whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearnerExtracurricular whereLearnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearnerExtracurricular wherePredicate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearnerExtracurricular whereSemesterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LearnerExtracurricular whereUpdatedAt($value)
 */
	class LearnerExtracurricular extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $type
 * @property string $notifiable_type
 * @property int $notifiable_id
 * @property string $data
 * @property string|null $read_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\NotificationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereNotifiableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereNotifiableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereUpdatedAt($value)
 */
	class Notification extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $description
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\PhaseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phase query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phase whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phase whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phase whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phase whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phase whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phase whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phase whereUpdatedAt($value)
 */
	class Phase extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $description
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Classes> $classes
 * @property-read int|null $classes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Learner> $learners
 * @property-read int|null $learners_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subject> $subjects
 * @property-read int|null $subjects_count
 * @method static \Database\Factories\ProgramFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereUpdatedAt($value)
 */
	class Program extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $source_class_id
 * @property int $destination_class_id
 * @property int|null $academic_year_id
 * @property \Illuminate\Support\Carbon|null $promoted_at
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademicYear|null $academicYear
 * @property-read \App\Models\Classes $destinationClass
 * @property-read \App\Models\Classes $sourceClass
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionMapping newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionMapping newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionMapping query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionMapping whereAcademicYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionMapping whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionMapping whereDestinationClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionMapping whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionMapping whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionMapping wherePromotedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionMapping whereSourceClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionMapping whereUpdatedAt($value)
 */
	class PromotionMapping extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $npsn
 * @property string|null $address
 * @property string|null $district
 * @property string|null $city
 * @property string|null $province
 * @property string|null $postal_code
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $website
 * @property string|null $logo
 * @property string|null $headmaster_name
 * @property string|null $headmaster_nip
 * @property string|null $headmaster_signature
 * @property string|null $school_stamp
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\SchoolProfileFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProfile whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProfile whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProfile whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProfile whereDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProfile whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProfile whereHeadmasterName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProfile whereHeadmasterNip($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProfile whereHeadmasterSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProfile whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProfile whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProfile whereNpsn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProfile wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProfile wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProfile whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProfile whereSchoolStamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProfile whereWebsite($value)
 */
	class SchoolProfile extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $academic_year_id
 * @property string $name
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AcademicYear $academicYear
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $attendances
 * @property-read int|null $attendances_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Grade> $grades
 * @property-read int|null $grades_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HomeroomNote> $homeroomNotes
 * @property-read int|null $homeroom_notes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LearnerExtracurricular> $learnerExtracurriculars
 * @property-read int|null $learner_extracurriculars_count
 * @method static \Database\Factories\SemesterFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Semester newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Semester newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Semester query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Semester whereAcademicYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Semester whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Semester whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Semester whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Semester whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Semester whereUpdatedAt($value)
 */
	class Semester extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $program_id
 * @property int|null $class_id
 * @property int|null $subject_group_id
 * @property string $code
 * @property string $name
 * @property string|null $description
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Classes|null $classes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Grade> $grades
 * @property-read int|null $grades_count
 * @property-read \App\Models\Program|null $program
 * @property-read \App\Models\SubjectGroup|null $subjectGroup
 * @method static \Database\Factories\SubjectFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject whereProgramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject whereSubjectGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subject whereUpdatedAt($value)
 */
	class Subject extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $sort_order
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\SubjectGroupFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubjectGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubjectGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubjectGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubjectGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubjectGroup whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubjectGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubjectGroup whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubjectGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubjectGroup whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubjectGroup whereUpdatedAt($value)
 */
	class SubjectGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $nip
 * @property string $name
 * @property string $gender
 * @property string|null $birth_place
 * @property \Illuminate\Support\Carbon|null $birth_date
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $photo
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\TutorFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tutor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tutor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tutor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tutor whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tutor whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tutor whereBirthPlace($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tutor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tutor whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tutor whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tutor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tutor whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tutor whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tutor whereNip($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tutor wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tutor wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tutor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tutor whereUserId($value)
 */
	class Tutor extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HomeroomTeacher> $homeroomTeachers
 * @property-read int|null $homeroom_teachers_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $teams
 * @property-read int|null $teams_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, bool $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, ?string $guard = null, bool $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User team($teams, bool $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, ?string $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTeam($teams)
 */
	class User extends \Eloquent implements \Filament\Models\Contracts\FilamentUser {}
}

