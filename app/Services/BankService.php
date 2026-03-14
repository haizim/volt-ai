<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Bank;
use Illuminate\Http\Request;

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
}
