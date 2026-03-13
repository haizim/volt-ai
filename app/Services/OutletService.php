<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Outlet;
use Illuminate\Http\Request;

final class OutletService
{
    public static function save(Request $request)
    {
        $me = auth()->user();

        $outlet = new Outlet;
        $outlet->name = $request->name;
        $outlet->address = $request->address;
        $outlet->city = $request->city;
        $outlet->province = $request->province;
        $outlet->postal_code = $request->postal_code;
        $outlet->user_id = $me->id;
        $outlet->save();
    }

    public static function get($id)
    {
        $me = auth()->user();

        return Outlet::where('user_id', $me->id)->find($id);
    }

    public static function update(Request $request, $id)
    {
        $me = auth()->user();

        $outlet = Outlet::where('user_id', $me->id)->find($id);
        $outlet->name = $request->name;
        $outlet->address = $request->address;
        $outlet->city = $request->city;
        $outlet->province = $request->province;
        $outlet->postal_code = $request->postal_code;
        $outlet->save();
    }

    public static function delete($id)
    {
        $me = auth()->user();
        Outlet::where('user_id', $me->id)->find($id)->delete();
    }
}
