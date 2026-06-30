<?php

namespace App\Services;

use App\Models\Classes;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelService
{
    public function downloadAttendanceTemplate(?int $classId = null): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $headers = ['nis', 'name', 'sick', 'permission', 'absent'];

        foreach (range('A', 'E') as $i => $col) {
            $sheet->setCellValue($col.'1', $headers[$i]);
            $sheet->getStyle($col.'1')->getFont()->setBold(true);
        }

        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(10);

        if ($classId) {
            $class = Classes::with('classLearners.learner')->find($classId);
            if ($class) {
                $row = 2;
                $sorted = $class->classLearners->sortBy(fn ($cl) => $cl->learner?->name ?? '');
                foreach ($sorted as $cl) {
                    $sheet->setCellValue('A'.$row, $cl->learner?->nis ?? '');
                    $sheet->setCellValue('B'.$row, $cl->learner?->name ?? '');
                    $sheet->setCellValue('C'.$row, 0);
                    $sheet->setCellValue('D'.$row, 0);
                    $sheet->setCellValue('E'.$row, 0);
                    $row++;
                }
            }
        }

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="template-presensi.xlsx"');

        return $response;
    }

    public function downloadGradeTemplate(?int $classId = null): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $headers = ['nis', 'name', 'task_score', 'pts_score', 'pas_score', 'practice_score'];

        foreach (range('A', 'F') as $i => $col) {
            $sheet->setCellValue($col.'1', $headers[$i]);
            $sheet->getStyle($col.'1')->getFont()->setBold(true);
        }

        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);

        if ($classId) {
            $class = Classes::with('classLearners.learner')->find($classId);
            if ($class) {
                $row = 2;
                $sorted = $class->classLearners->sortBy(fn ($cl) => $cl->learner?->name ?? '');
                foreach ($sorted as $cl) {
                    $sheet->setCellValue('A'.$row, $cl->learner?->nis ?? '');
                    $sheet->setCellValue('B'.$row, $cl->learner?->name ?? '');
                    $row++;
                }
            }
        }

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="template-nilai.xlsx"');

        return $response;
    }

    public function exportGrades(iterable $grades, string $subjectName, string $className, string $academicYear, string $semester): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'NIS');
        $sheet->setCellValue('B1', 'Nama');
        $sheet->setCellValue('C1', 'Nilai Tugas');
        $sheet->setCellValue('D1', 'Nilai PTS');
        $sheet->setCellValue('E1', 'Nilai PAS');
        $sheet->setCellValue('F1', 'Nilai Praktik');
        $sheet->setCellValue('G1', 'Nilai Akhir');
        $sheet->setCellValue('H1', 'Predikat');

        foreach (range('A', 'H') as $col) {
            $sheet->getStyle($col.'1')->getFont()->setBold(true);
        }

        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(12);

        $row = 2;
        $sorted = collect($grades)->sortBy(fn ($g) => $g->learner?->name ?? '');
        foreach ($sorted as $grade) {
            $sheet->setCellValue('A'.$row, $grade->learner?->nis ?? '');
            $sheet->setCellValue('B'.$row, $grade->learner?->name ?? '');
            $sheet->setCellValue('C'.$row, $grade->task_score);
            $sheet->setCellValue('D'.$row, $grade->pts_score);
            $sheet->setCellValue('E'.$row, $grade->pas_score);
            $sheet->setCellValue('F'.$row, $grade->practice_score);
            $sheet->setCellValue('G'.$row, $grade->final_score);
            $sheet->setCellValue('H'.$row, $grade->predicate);
            $row++;
        }

        $filename = "nilai-{$subjectName}-{$className}-{$academicYear}-{$semester}.xlsx";
        $cleanName = preg_replace('/[^\w\-\.]/', '_', $filename);

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$cleanName.'"');

        app(AuditService::class)->logExport('grade', [
            'subject' => $subjectName,
            'class' => $className,
            'academic_year' => $academicYear,
            'semester' => $semester,
        ]);

        return $response;
    }

    public function exportPivotGrades(iterable $records, $subjects, string $className, string $academicYear, string $semester): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Peserta Didik');
        $sheet->setCellValue('B1', 'Tahun Ajaran');
        $sheet->setCellValue('C1', 'Semester');
        $sheet->setCellValue('D1', 'NIS');
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(15);

        $col = 'E';
        $subjectHeaders = [];
        foreach ($subjects as $subject) {
            $sheet->setCellValue($col.'1', $subject->name);
            $sheet->getColumnDimension($col)->setWidth(15);
            $subjectHeaders[$col] = (int) $subject->id;
            $col++;
        }

        $lastCol = $col;
        foreach (range('A', $lastCol) as $c) {
            $sheet->getStyle($c.'1')->getFont()->setBold(true);
        }

        $row = 2;
        foreach ($records as $record) {
            $sheet->setCellValue('A'.$row, $record->learner_name);
            $sheet->setCellValue('B'.$row, $record->academic_year);
            $sheet->setCellValue('C'.$row, $record->semester);
            $sheet->setCellValue('D'.$row, $record->nis);

            foreach ($subjectHeaders as $c => $subjectId) {
                $value = $record->{"subject_{$subjectId}"};
                $sheet->setCellValue($c.$row, $value ?? '-');
            }
            $row++;
        }

        $filename = "pivot-nilai-{$className}-{$academicYear}-{$semester}.xlsx";
        $cleanName = preg_replace('/[^\w\-\.]/', '_', $filename);

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$cleanName.'"');

        return $response;
    }

    public function exportAttendancePivot(iterable $records, string $academicYear, string $semester): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'NIS');
        $sheet->setCellValue('B1', 'Peserta Didik');
        $sheet->setCellValue('C1', 'Tahun Ajaran');
        $sheet->setCellValue('D1', 'Semester');
        $sheet->setCellValue('E1', 'Sakit');
        $sheet->setCellValue('F1', 'Izin');
        $sheet->setCellValue('G1', 'Tanpa Keterangan');
        $sheet->setCellValue('H1', 'Total');

        foreach (range('A', 'H') as $col) {
            $sheet->getStyle($col.'1')->getFont()->setBold(true);
        }

        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(18);
        $sheet->getColumnDimension('H')->setWidth(10);

        $row = 2;
        foreach ($records as $record) {
            $total = (int) ($record->sick ?? 0) + (int) ($record->permission ?? 0) + (int) ($record->absent ?? 0);
            $sheet->setCellValue('A'.$row, $record->nis ?? '');
            $sheet->setCellValue('B'.$row, $record->learner_name);
            $sheet->setCellValue('C'.$row, $record->academic_year);
            $sheet->setCellValue('D'.$row, $record->semester);
            $sheet->setCellValue('E'.$row, $record->sick);
            $sheet->setCellValue('F'.$row, $record->permission);
            $sheet->setCellValue('G'.$row, $record->absent);
            $sheet->setCellValue('H'.$row, $total);
            $row++;
        }

        $filename = "presensi-{$academicYear}-{$semester}.xlsx";
        $cleanName = preg_replace('/[^\w\-\.]/', '_', $filename);

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$cleanName.'"');

        return $response;
    }

    public function exportHomeroomNotePivot(iterable $records, string $academicYear, string $semester): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'NIS');
        $sheet->setCellValue('B1', 'Peserta Didik');
        $sheet->setCellValue('C1', 'Tahun Ajaran');
        $sheet->setCellValue('D1', 'Semester');
        $sheet->setCellValue('E1', 'Catatan');

        foreach (range('A', 'E') as $col) {
            $sheet->getStyle($col.'1')->getFont()->setBold(true);
        }

        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(60);

        $row = 2;
        foreach ($records as $record) {
            $sheet->setCellValue('A'.$row, $record->nis ?? '');
            $sheet->setCellValue('B'.$row, $record->learner_name);
            $sheet->setCellValue('C'.$row, $record->academic_year);
            $sheet->setCellValue('D'.$row, $record->semester);
            $sheet->setCellValue('E'.$row, $record->note);
            $row++;
        }

        $filename = "catatan-{$academicYear}-{$semester}.xlsx";
        $cleanName = preg_replace('/[^\w\-\.]/', '_', $filename);

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$cleanName.'"');

        return $response;
    }

    public function downloadHomeroomNoteTemplate(?int $classId = null): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $headers = ['nis', 'name', 'note'];

        foreach (range('A', 'C') as $i => $col) {
            $sheet->setCellValue($col.'1', $headers[$i]);
            $sheet->getStyle($col.'1')->getFont()->setBold(true);
        }

        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(60);

        if ($classId) {
            $class = Classes::with('classLearners.learner')->find($classId);
            if ($class) {
                $row = 2;
                $sorted = $class->classLearners->sortBy(fn ($cl) => $cl->learner?->name ?? '');
                foreach ($sorted as $cl) {
                    $sheet->setCellValue('A'.$row, $cl->learner?->nis ?? '');
                    $sheet->setCellValue('B'.$row, $cl->learner?->name ?? '');
                    $row++;
                }
            }
        }

        $filename = 'template-catatan.xlsx';

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');

        return $response;
    }

    public function downloadExtracurricularTemplate(?int $classId = null): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $headers = ['nis', 'name', 'grade'];

        foreach (range('A', 'C') as $i => $col) {
            $sheet->setCellValue($col.'1', $headers[$i]);
            $sheet->getStyle($col.'1')->getFont()->setBold(true);
        }

        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);

        if ($classId) {
            $class = Classes::with('classLearners.learner')->find($classId);
            if ($class) {
                $row = 2;
                $sorted = $class->classLearners->sortBy(fn ($cl) => $cl->learner?->name ?? '');
                foreach ($sorted as $cl) {
                    $sheet->setCellValue('A'.$row, $cl->learner?->nis ?? '');
                    $sheet->setCellValue('B'.$row, $cl->learner?->name ?? '');
                    $row++;
                }
            }
        }

        $filename = 'template-ekstrakurikuler.xlsx';

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');

        return $response;
    }

    public function exportExtracurricularPivot(iterable $records, string $academicYear, string $semester, ?Collection $extracurriculars = null): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Peserta Didik');
        $sheet->setCellValue('B1', 'Tahun Ajaran');
        $sheet->setCellValue('C1', 'Semester');

        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(12);

        $col = 'D';
        $extraColumns = [];
        $firstRecord = $records->first();
        if ($firstRecord) {
            foreach (get_object_vars($firstRecord) as $key => $val) {
                if (str_starts_with($key, 'extra_')) {
                    $extraId = substr($key, 6);
                    $name = $extracurriculars?->get($extraId) ?? $extraId;
                    $sheet->setCellValue($col.'1', $name);
                    $sheet->getColumnDimension($col)->setWidth(15);
                    $extraColumns[$col] = $key;
                    $col++;
                }
            }
        }

        $lastCol = $col;
        foreach (range('A', $lastCol) as $c) {
            $sheet->getStyle($c.'1')->getFont()->setBold(true);
        }

        $row = 2;
        foreach ($records as $record) {
            $sheet->setCellValue('A'.$row, $record->learner_name);
            $sheet->setCellValue('B'.$row, $record->academic_year);
            $sheet->setCellValue('C'.$row, $record->semester);

            foreach ($extraColumns as $c => $key) {
                $value = $record->{$key};
                $sheet->setCellValue($c.$row, $value ?? '-');
            }
            $row++;
        }

        $filename = "pivot-ekstrakurikuler-{$academicYear}-{$semester}.xlsx";
        $cleanName = preg_replace('/[^\w\-\.]/', '_', $filename);

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$cleanName.'"');

        return $response;
    }
}
