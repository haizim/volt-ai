<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\Permission;
use App\Http\Requests\BankImportRequest;
use App\Http\Requests\BankRequest;
use App\Models\Bank;
use App\Services\BankService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class BankController
{
    public function index(): View
    {
        return view('banks.index');
    }

    public function create(): View
    {
        Gate::authorize(Permission::BANK_MANAGE);

        return view('banks.create');
    }

    public function store(BankRequest $request): RedirectResponse
    {
        BankService::save($request);

        return redirect()->route('banks.index')
            ->withSuccess('Bank berhasil ditambahkan');
    }

    public function show(string $id): View
    {
        $bank = BankService::get((int) $id);

        return view('banks.show', compact('bank'));
    }

    public function edit(string $id): View
    {
        Gate::authorize(Permission::BANK_MANAGE);
        $bank = BankService::get((int) $id);

        return view('banks.edit', compact('bank'));
    }

    public function update(BankRequest $request, string $id): RedirectResponse
    {
        BankService::update($request, (int) $id);

        return redirect()->route('banks.index')
            ->withSuccess('Bank berhasil diperbarui');
    }

    public function destroy(Bank $bank): RedirectResponse
    {
        BankService::delete((int) $bank->id);

        return redirect()->route('banks.index')
            ->withSuccess('Bank berhasil dihapus');
    }

    public function export(): BinaryFileResponse
    {
        Gate::authorize(Permission::BANK_VIEW);

        return BankService::export();
    }

    public function template(): BinaryFileResponse
    {
        return BankService::template();
    }

    public function import(BankImportRequest $request): RedirectResponse
    {
        BankService::import($request->file('file'));

        return redirect()->route('banks.index')
            ->withSuccess('Banks imported successfully');
    }
}
