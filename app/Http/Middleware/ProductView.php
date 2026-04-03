<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\Permission;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ProductView
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $route = $request->route();
        $action = $route?->getActionMethod();

        $manageActions = ['create', 'store', 'edit', 'update', 'destroy'];

        if (in_array($action, $manageActions)) {
            if ($user->can(Permission::PRODUCT_MANAGE)) {
                return $next($request);
            }
        } else {
            if ($user->can(Permission::PRODUCT_VIEW)) {
                return $next($request);
            }
        }

        return abort(401);
    }
}
