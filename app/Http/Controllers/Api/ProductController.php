<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\Permission;
use App\Http\Requests\ProductRequest;
use App\Http\Responses\ApiResponse;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

final class ProductController
{
    public function index(Request $request): JsonResponse
    {
        return ApiResponse::success(ProductService::search($request));
    }

    public function store(ProductRequest $request): JsonResponse
    {
        return ApiResponse::success(ProductService::save($request));
    }

    public function show(int $id): JsonResponse
    {
        return ApiResponse::success(ProductService::get($id));
    }

    public function update(ProductRequest $request, int $id): JsonResponse
    {
        return ApiResponse::success(ProductService::update($request, $id));
    }

    public function destroy(int $id): JsonResponse
    {
        Gate::authorize(Permission::PRODUCT_MANAGE);

        ProductService::delete($id);

        return ApiResponse::success();
    }
}
