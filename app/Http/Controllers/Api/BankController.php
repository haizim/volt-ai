<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\BankRequest;
use App\Http\Responses\ApiResponse;
use App\Services\BankService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class BankController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $banks = BankService::search($request);

        return ApiResponse::success($banks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BankRequest $request): JsonResponse
    {
        $bank = BankService::save($request);

        return ApiResponse::success($bank);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $bank = BankService::get($id);

        return ApiResponse::success($bank);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BankRequest $request, int $id): JsonResponse
    {
        $bank = BankService::update($request, $id);

        return ApiResponse::success($bank);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        BankService::delete($id);

        return ApiResponse::success();
    }
}
