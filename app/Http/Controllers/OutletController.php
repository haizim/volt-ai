<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\OutletRequest;
use App\Models\Outlet;
use App\Services\OutletService;

final class OutletController
{
    public function index()
    {
        return view('outlet.index');
    }

    public function create()
    {
        return view('outlet.create');
    }

    public function store(OutletRequest $request)
    {
        OutletService::save($request);

        return redirect()->route('outlet.index')->withSuccess('Outlet berhasil disimpan');
    }

    public function show(string $id)
    {
        $outlet = OutletService::get($id);

        return view('outlet.show', compact('outlet'));
    }

    public function edit(string $id)
    {
        $outlet = OutletService::get($id);

        return view('outlet.edit', compact('outlet'));
    }

    public function update(OutletRequest $request, string $id)
    {
        OutletService::update($request, $id);

        return redirect()->route('outlet.index')->withSuccess('Outlet berhasil diperbarui');
    }

    public function destroy(Outlet $outlet)
    {
        OutletService::delete($outlet->id);

        return redirect()->route('outlet.index')->withSuccess('Outlet berhasil dihapus');
    }
}
