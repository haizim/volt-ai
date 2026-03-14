<?php

declare(strict_types=1);

namespace App\Services;

use App\Exports\BankExport;
use App\Models\Bank;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class BankService
{
    public static function save(Request $request): Bank
    {
        return Bank::create($request->all());
    }

    public static function get(int $id): Bank
    {
        return Bank::findOrFail($id);
    }

    public static function update(Request $request, int $id): Bank
    {
        $bank = Bank::findOrFail($id);
        $bank->update($request->all());

        return $bank;
    }

    public static function delete(int $id): bool
    {
        $bank = Bank::findOrFail($id);

        return $bank->delete();
    }

    public static function export(): BinaryFileResponse
    {
        $now = date('Ymd-His');
        $fileName = "bank-{$now}.xlsx";

        return Excel::download(new BankExport, $fileName);
    }
}
