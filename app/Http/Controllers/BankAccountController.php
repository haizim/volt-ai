<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\Permission;
use App\Http\Requests\BankAccountRequest;
use App\Models\BankAccount;
use App\Services\BankAccountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class BankAccountController
{
    public function index(): View
    {
        return view('bank-accounts.index');
    }

    public function create(): View
    {
        Gate::authorize(Permission::BANK_ACCOUNT_MANAGE);

        return view('bank-accounts.create');
    }

    public function store(BankAccountRequest $request): RedirectResponse
    {
        BankAccountService::save($request);

        return redirect()->route('bank-accounts.index')
            ->withSuccess('Bank account created successfully');
    }

    public function show(int $id): View
    {
        $bankAccount = BankAccountService::get($id);

        return view('bank-accounts.show', compact('bankAccount'));
    }

    public function edit(int $id): View
    {
        Gate::authorize(Permission::BANK_ACCOUNT_MANAGE);
        $bankAccount = BankAccountService::get($id);

        return view('bank-accounts.edit', compact('bankAccount'));
    }

    public function update(BankAccountRequest $request, int $id): RedirectResponse
    {
        BankAccountService::update($request, $id);

        return redirect()->route('bank-accounts.index')
            ->withSuccess('Bank account updated successfully');
    }

    public function destroy(BankAccount $bankAccount): RedirectResponse
    {
        Gate::authorize(Permission::BANK_ACCOUNT_MANAGE);
        BankAccountService::delete($bankAccount->id);

        return redirect()->route('bank-accounts.index')
            ->withSuccess('Bank account deleted successfully');
    }

    public function export(Request $request): BinaryFileResponse
    {
        Gate::authorize(Permission::BANK_ACCOUNT_VIEW);

        return BankAccountService::export((int) $request->bank_id);
    }
}
