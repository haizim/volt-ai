<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\BankAccount;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

final class BankAccountExport implements FromQuery, ShouldAutoSize, WithColumnFormatting, WithHeadings, WithMapping
{
    private $bankId;

    public function __construct($bankId)
    {
        $this->bankId = $bankId;
    }

    public function query()
    {
        return BankAccount::query()
            ->where('bank_id', $this->bankId)
            ->with('bank')
            ->orderBy('account_number', 'asc');
    }

    /**
     * Mapping data
     *
     * @param  BankAccount  $data
     */
    public function map($data): array
    {
        return [
            $data->bank->name,
            $data->account_number,
            $data->by_name,
            $data->updated_at->format('d/m/Y H:i'),
        ];
    }

    public function headings(): array
    {
        return [
            'Bank Name',
            'Account Number',
            'Account Name',
            'Updated At',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => 'dd/mm/yyyy h:mm',
        ];
    }
}
