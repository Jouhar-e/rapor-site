<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasAcademicYearScope
{
    protected static function bootHasAcademicYearScope(): void
    {
        static::addGlobalScope('not-archived-academic-year', function (Builder $builder) {
            $from = $builder->getQuery()->from;

            if (! is_string($from)) {
                return;
            }

            if (mb_stripos($from, ' as ') !== false) {
                $parts = preg_split('/\s+as\s+/i', $from, 2);
                $ref = trim($parts[1] ?? $parts[0], '`"\'');
            } else {
                $ref = $from;
            }

            $builder->whereRaw(
                "exists (select 1 from `academic_years` where `academic_years`.`id` = `{$ref}`.`academic_year_id` and `academic_years`.`is_archived` = 0)"
            );
        });
    }
}
