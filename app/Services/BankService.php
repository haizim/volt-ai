<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class BankService
{
    public static function save(Request $request): Bank
    {
        return DB::transaction(function () use ($request) {
            return Bank::create([
                'name' => $request->name,
                'code' => $request->code,
            ]);
        });
    }

    public static function get(int $id): Bank
    {
        return Bank::findOrFail($id);
    }

    public static function update(Request $request, int $id): Bank
    {
        return DB::transaction(function () use ($request, $id) {
            $bank = Bank::findOrFail($id);
            $bank->update([
                'name' => $request->name,
                'code' => $request->code,
            ]);

            return $bank->fresh();
        });
    }

    public static function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $bank = Bank::findOrFail($id);
            $bank->delete();
        });
    }
}
