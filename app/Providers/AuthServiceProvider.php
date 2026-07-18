<?php

namespace App\Providers;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\AuditLog;
use App\Models\BackupHistory;
use App\Models\Classes;
use App\Models\ClassLearner;
use App\Models\Extracurricular;
use App\Models\Grade;
use App\Models\HomeroomNote;
use App\Models\HomeroomTeacher;
use App\Models\Learner;
use App\Models\LearnerExtracurricular;
use App\Models\Phase;
use App\Models\Program;
use App\Models\PromotionMapping;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\SubjectGroup;
use App\Models\Tutor;
use App\Policies\AcademicYearPolicy;
use App\Policies\AttendancePolicy;
use App\Policies\AuditLogPolicy;
use App\Policies\BackupHistoryPolicy;
use App\Policies\ClassesPolicy;
use App\Policies\ClassLearnerPolicy;
use App\Policies\ExtracurricularPolicy;
use App\Policies\GradePolicy;
use App\Policies\HomeroomNotePolicy;
use App\Policies\HomeroomTeacherPolicy;
use App\Policies\LearnerExtracurricularPolicy;
use App\Policies\LearnerPolicy;
use App\Policies\PhasePolicy;
use App\Policies\ProgramPolicy;
use App\Policies\PromotionMappingPolicy;
use App\Policies\SemesterPolicy;
use App\Policies\SubjectGroupPolicy;
use App\Policies\SubjectPolicy;
use App\Policies\TutorPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Program::class => ProgramPolicy::class,
        AcademicYear::class => AcademicYearPolicy::class,
        Semester::class => SemesterPolicy::class,
        Tutor::class => TutorPolicy::class,
        Learner::class => LearnerPolicy::class,
        Classes::class => ClassesPolicy::class,
        Subject::class => SubjectPolicy::class,
        Extracurricular::class => ExtracurricularPolicy::class,
        HomeroomTeacher::class => HomeroomTeacherPolicy::class,
        ClassLearner::class => ClassLearnerPolicy::class,
        Grade::class => GradePolicy::class,
        Attendance::class => AttendancePolicy::class,
        LearnerExtracurricular::class => LearnerExtracurricularPolicy::class,
        HomeroomNote::class => HomeroomNotePolicy::class,
        Phase::class => PhasePolicy::class,
        PromotionMapping::class => PromotionMappingPolicy::class,
        SubjectGroup::class => SubjectGroupPolicy::class,
        AuditLog::class => AuditLogPolicy::class,
        BackupHistory::class => BackupHistoryPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
