<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BankAccount;
use Illuminate\Http\Request;

final class BankAccountService
{
    public static function save(Request $request): BankAccount
    {
        return BankAccount::create($request->all());
    }

    public static function get(int $id): BankAccount
    {
        return BankAccount::findOrFail($id);
    }

    public static function update(Request $request, int $id): BankAccount
    {
        $bankAccount = BankAccount::findOrFail($id);
        $bankAccount->update($request->all());

        return $bankAccount;
    }

    public static function delete(int $id): bool
    {
        $bankAccount = BankAccount::findOrFail($id);

        return $bankAccount->delete();
    }
}
