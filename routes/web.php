<?php

use App\Services\ExcelService;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');

Route::get('/grade/template/{class?}', function ($class = null) {
    return app(ExcelService::class)->downloadGradeTemplate($class ? (int) $class : null);
})->middleware(['web', 'auth'])->name('grade.template');

Route::get('/attendance/template/{class?}', function ($class = null) {
    return app(ExcelService::class)->downloadAttendanceTemplate($class ? (int) $class : null);
})->middleware(['web', 'auth'])->name('attendance.template');

Route::get('/homeroom-note/template/{class?}', function ($class = null) {
    return app(ExcelService::class)->downloadHomeroomNoteTemplate($class ? (int) $class : null);
})->middleware(['web', 'auth'])->name('homeroom-note.template');

Route::get('/extracurricular/template/{class?}', function ($class = null) {
    return app(ExcelService::class)->downloadExtracurricularTemplate($class ? (int) $class : null);
})->middleware(['web', 'auth'])->name('extracurricular.template');
