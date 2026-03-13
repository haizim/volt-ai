<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\BankRequest;
use App\Models\Bank;
use App\Services\BankService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class BankController
{
    public function index(): View
    {
        return view('banks.index');
    }

    public function create(): View
    {
        return view('banks.create');
    }

    public function store(BankRequest $request): RedirectResponse
    {
        try {
            BankService::save($request);

            return redirect()->route('banks.index')
                ->withSuccess('Bank berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function show(string $id): View
    {
        $bank = BankService::get((int) $id);

        return view('banks.show', compact('bank'));
    }

    public function edit(string $id): View
    {
        $bank = BankService::get((int) $id);

        return view('banks.edit', compact('bank'));
    }

    public function update(BankRequest $request, string $id): RedirectResponse
    {
        try {
            BankService::update($request, (int) $id);

            return redirect()->route('banks.index')
                ->withSuccess('Bank berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Bank $bank): RedirectResponse
    {
        try {
            BankService::delete((int) $bank->id);

            return redirect()->route('banks.index')
                ->withSuccess('Bank berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}
