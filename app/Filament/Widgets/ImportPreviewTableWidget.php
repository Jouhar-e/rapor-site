<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class ImportPreviewTableWidget extends Widget
{
    protected string $view = 'filament.widgets.import-preview-table';

    public array $data = [];

    public array $headers = [];

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }
}
