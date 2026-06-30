<?php

namespace App\Services\DTOs;

class ImportResult
{
    public function __construct(
        public readonly bool $success,
        public readonly int $imported = 0,
        public readonly int $updated = 0,
        public readonly int $skipped = 0,
        public readonly array $errors = [],
    ) {}

    public function total(): int
    {
        return $this->imported + $this->updated + $this->skipped;
    }
}
