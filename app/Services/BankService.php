<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Permission;
use App\Exports\BankExport;
use App\Exports\BankTemplateExport;
use App\Imports\BankImport;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class BankService
{
    public static function save(Request $request): Bank
    {
        Gate::authorize(Permission::BANK_MANAGE);

        return Bank::create($request->all());
    }

    public static function get(int $id): Bank
    {
        return Bank::findOrFail($id);
    }

    public static function update(Request $request, int $id): Bank
    {
        Gate::authorize(Permission::BANK_MANAGE);

        $bank = Bank::findOrFail($id);
        $bank->update($request->all());

        return $bank;
    }

    public static function delete(int $id): bool
    {
        Gate::authorize(Permission::BANK_MANAGE);

        $bank = Bank::findOrFail($id);

        return $bank->delete();
    }

    public static function export(): BinaryFileResponse
    {
        $now = date('Ymd-His');
        $fileName = "bank-{$now}.xlsx";

        return Excel::download(new BankExport, $fileName);
    }

    public static function template(): BinaryFileResponse
    {
        return Excel::download(new BankTemplateExport, 'bank-template.xlsx');
    }

    public static function import($file): void
    {
        Excel::import(new BankImport, $file);
    }
}
