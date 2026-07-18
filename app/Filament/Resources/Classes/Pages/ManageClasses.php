<?php

namespace App\Filament\Resources\Classes\Pages;

use App\Filament\Resources\Classes\ClassesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageClasses extends ManageRecords
{
    protected static string $resource = ClassesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateDataUsing(function (array $data): array {
                    $classNum = '';
                    preg_match('/(\d+)/', $data['name'] ?? '', $matches);
                    $classNum = $matches[1] ?? '';

                    $newCodes = [];

                    foreach ($data['subjects'] ?? [] as $key => $subject) {
                        if (empty($subject['code']) && ! empty($subject['name'])) {
                            $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $subject['name']), 0, 3));
                            $baseCode = $prefix.($classNum ? '-'.$classNum : '');

                            $code = $baseCode;
                            $counter = 1;
                            while (in_array($code, $newCodes)) {
                                $code = $baseCode.'_'.$counter;
                                $counter++;
                            }
                            $newCodes[] = $code;

                            $data['subjects'][$key]['code'] = $code;
                        }
                    }

                    return $data;
                }),
        ];
    }
}
