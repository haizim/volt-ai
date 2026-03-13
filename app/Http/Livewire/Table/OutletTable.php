<?php

declare(strict_types=1);

namespace App\Http\Livewire\Table;

use App\Enums\Permission;
use App\Models\Outlet;
use Laravolt\Suitable\Columns\RestfulButton;
use Laravolt\Suitable\Columns\RowNumber;
use Laravolt\Suitable\Columns\Text;
use Laravolt\Ui\TableView;

final class OutletTable extends TableView
{
    public function data()
    {
        $me = auth()->user();
        $query = Outlet::query()->where('user_id', $me->id);

        if ($this->sortPayload()['sort']) {
            $query = $query->autoSort($this->sortPayload());
        } else {
            $query = $query->orderBy('name', 'asc');
        }

        $search = $this->search;
        if ($search) {
            $query->whereLike(['name', 'city', 'province'], $search);
        }

        return $query;
    }

    public function columns(): array
    {
        $only = ['show'];
        if (auth()->user()->can(Permission::OUTLET_MANAGE)) {
            $only = array_merge($only, (['edit', 'destroy']));
        }

        return [
            RowNumber::make('#'),
            Text::make('name', 'Nama')->sortable(),
            Text::make('city', 'Kota')->sortable(),
            Text::make('province', 'Provinsi')->sortable(),
            Text::make('postal_code', 'Kode Pos')->sortable(),
            RestfulButton::make('outlet')->only($only),
        ];
    }
}
