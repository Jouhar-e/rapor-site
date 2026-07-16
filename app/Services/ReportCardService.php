<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassLearner;
use App\Models\HomeroomNote;
use App\Models\HomeroomTeacher;
use App\Models\Learner;
use App\Models\LearnerExtracurricular;
use App\Models\SchoolProfile;
use App\Models\Semester;
use App\Models\Tutor;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportCardService
{
    protected string $chromePath;

    public function __construct()
    {
        $cacheDir = getenv('USERPROFILE').'\.cache\puppeteer';
        $chromeDir = $cacheDir.'\chrome';
        if (is_dir($chromeDir)) {
            $versions = scandir($chromeDir, SCANDIR_SORT_DESCENDING);
            foreach ($versions as $version) {
                if ($version === '.' || $version === '..') {
                    continue;
                }
                $exePath = $chromeDir.'\\'.$version.'\chrome-win64\chrome.exe';
                if (file_exists($exePath)) {
                    $this->chromePath = $exePath;
                    break;
                }
            }
        }
    }

    public function getChromePath(): string
    {
        if ($this->chromePath) {
            return $this->chromePath;
        }

        return '';
    }

    public function setChromePath(string $path): void
    {
        $this->chromePath = $path;
    }

    public function generatePdf(
        int $learnerId,
        int $academicYearId,
        int $semesterId,
        array $sections = ['cover', 'identitas', 'biodata', 'nilai'],
    ): StreamedResponse {
        $data = $this->loadData($learnerId, $academicYearId, $semesterId);
        $html = $this->renderHtml($data, $sections);

        $learner = $data['learner'];
        $filename = 'Rapor_'.str_replace(' ', '_', $learner->name).'_'.$data['academicYear']->name.'.pdf';

        $browsershot = Browsershot::html($html)
            ->format('A4')
            ->margins(15, 15, 15, 20)
            ->showBackground()
            ->waitUntilNetworkIdle()
            ->setNodeBinary('C:\Program Files\nodejs\node.exe')
            ->addChromiumArguments(['no-sandbox', 'disable-gpu']);

        if ($chromePath = $this->getChromePath()) {
            $browsershot->setChromePath($chromePath);
        }

        $tempPdf = tempnam(sys_get_temp_dir(), 'rapot_').'.pdf';
        $browsershot->save($tempPdf);

        return response()->stream(function () use ($tempPdf) {
            readfile($tempPdf);
            unlink($tempPdf);
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function generateZip(
        array $learnerIds,
        int $academicYearId,
        int $semesterId,
        array $sections = ['cover', 'identitas', 'biodata', 'nilai'],
    ): StreamedResponse {
        $zip = new \ZipArchive;
        $tempZip = tempnam(sys_get_temp_dir(), 'rapot_zip_').'.zip';

        if ($zip->open($tempZip, \ZipArchive::CREATE) !== true) {
            abort(500, 'Gagal membuat file ZIP');
        }

        $chromePath = $this->getChromePath();

        foreach ($learnerIds as $learnerId) {
            $data = $this->loadData((int) $learnerId, $academicYearId, $semesterId);
            $html = $this->renderHtml($data, $sections);

            $browsershot = Browsershot::html($html)
                ->format('A4')
                ->margins(15, 15, 15, 20)
                ->showBackground()
                ->waitUntilNetworkIdle()
                ->setNodeBinary('C:\Program Files\nodejs\node.exe')
                ->addChromiumArguments(['no-sandbox', 'disable-gpu']);

            if ($chromePath) {
                $browsershot->setChromePath($chromePath);
            }

            $tempPdf = tempnam(sys_get_temp_dir(), 'rapot_').'.pdf';
            $browsershot->save($tempPdf);

            $learner = $data['learner'];
            $filename = 'Rapor_'.str_replace(['/', '\\', ' '], '_', $learner->name).'.pdf';
            $zip->addFile($tempPdf, $filename);
        }

        $zip->close();

        return response()->stream(function () use ($tempZip) {
            readfile($tempZip);
            unlink($tempZip);
        }, 200, [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename="Rapor_Batch.zip"',
        ]);
    }

    protected function loadData(int $learnerId, int $academicYearId, int $semesterId): array
    {
        $school = SchoolProfile::first();
        $academicYear = AcademicYear::findOrFail($academicYearId);
        $semester = Semester::findOrFail($semesterId);
        $learner = Learner::with('program')->findOrFail($learnerId);

        $classLearner = ClassLearner::where('learner_id', $learnerId)
            ->where('academic_year_id', $academicYearId)
            ->where('semester_id', $semesterId)
            ->with('classes.phase', 'classes.program')
            ->first();

        $class = $classLearner?->classes;
        $phaseName = $class?->phase?->name ?? '—';
        $programName = strtoupper($learner->program?->code ?? $class?->program?->code ?? '—');

        $homeroomTeacher = HomeroomTeacher::where('class_id', $class?->id)
            ->where('academic_year_id', $academicYearId)
            ->with('user')
            ->first();

        $homeroomTeacherNip = null;
        if ($homeroomTeacher?->user_id) {
            $homeroomTeacherNip = Tutor::where('user_id', $homeroomTeacher->user_id)->value('nip');
        }

        $grades = $learner->grades()
            ->where('academic_year_id', $academicYearId)
            ->where('semester_id', $semesterId)
            ->with('subject.subjectGroup')
            ->get();

        $groupedGrades = $this->groupGradesBySubjectGroup($grades);

        $attendance = Attendance::select([
            'learner_id',
            DB::raw('SUM(sick) as total_sick'),
            DB::raw('SUM(permission) as total_permission'),
            DB::raw('SUM(absent) as total_absent'),
        ])
            ->where('learner_id', $learnerId)
            ->where('academic_year_id', $academicYearId)
            ->where('semester_id', $semesterId)
            ->groupBy('learner_id')
            ->first();

        $extracurriculars = LearnerExtracurricular::where('learner_id', $learnerId)
            ->where('academic_year_id', $academicYearId)
            ->where('semester_id', $semesterId)
            ->with('extracurricular')
            ->get();

        $homeroomNote = HomeroomNote::where('learner_id', $learnerId)
            ->where('academic_year_id', $academicYearId)
            ->where('semester_id', $semesterId)
            ->first();

        return compact(
            'school', 'academicYear', 'semester', 'learner',
            'class', 'phaseName', 'programName',
            'homeroomTeacher', 'homeroomTeacherNip', 'groupedGrades', 'attendance',
            'extracurriculars', 'homeroomNote',
        );
    }

    protected function renderHtml(array $data, array $sections): string
    {
        $html = '';

        if (in_array('cover', $sections)) {
            $html .= view('pdf.rapot.cover', $data)->render();
        }

        if (in_array('identitas', $sections)) {
            $html .= view('pdf.rapot.identitas', $data)->render();
        }

        if (in_array('biodata', $sections)) {
            $html .= view('pdf.rapot.biodata', $data)->render();
        }

        if (in_array('nilai', $sections)) {
            $html .= view('pdf.rapot.nilai', $data)->render();
        }

        return $html;
    }

    protected function groupGradesBySubjectGroup(Collection $grades): array
    {
        $grouped = [];

        foreach ($grades as $grade) {
            $groupName = $grade->subject?->subjectGroup?->name ?? 'Lainnya';

            if (! isset($grouped[$groupName])) {
                $grouped[$groupName] = [];
            }

            $grouped[$groupName][] = $grade;
        }

        return $grouped;
    }
}
