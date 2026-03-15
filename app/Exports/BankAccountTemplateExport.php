<?php

declare(strict_types=1);

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

final class BankAccountTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new BankAccountImportTemplateExport,
            new BankExport,
        ];
    }
}
