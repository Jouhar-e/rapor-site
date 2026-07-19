<?php

namespace App\Providers;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Classes;
use App\Models\ClassLearner;
use App\Models\Extracurricular;
use App\Models\Grade;
use App\Models\GradePredicate;
use App\Models\GradingSetting;
use App\Models\HomeroomNote;
use App\Models\HomeroomTeacher;
use App\Models\Learner;
use App\Models\LearnerExtracurricular;
use App\Models\Notification;
use App\Models\Program;
use App\Models\PromotionMapping;
use App\Models\SchoolProfile;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\Tutor;
use App\Models\User;
use App\Observers\AuditLogObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        try {
            if (Schema::hasTable('school_profiles')) {
                $school = SchoolProfile::first();
                if ($school?->name) {
                    config(['app.name' => $school->name]);
                }
            }
        } catch (\Throwable) {
        }

        $models = [
            AcademicYear::class,
            Attendance::class,
            ClassLearner::class,
            Classes::class,
            Extracurricular::class,
            Grade::class,
            GradePredicate::class,
            GradingSetting::class,
            HomeroomNote::class,
            HomeroomTeacher::class,
            Learner::class,
            LearnerExtracurricular::class,
            Notification::class,
            Program::class,
            PromotionMapping::class,
            SchoolProfile::class,
            Semester::class,
            Subject::class,
            Tutor::class,
            User::class,
        ];

        foreach ($models as $model) {
            $model::observe(AuditLogObserver::class);
        }
        URL::forceScheme('https');
    }
}
