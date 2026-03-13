<?php

declare(strict_types=1);

namespace App\Http\Livewire\Table;

use App\Enums\Permission;
use App\Models\Product;
use Laravolt\Suitable\Columns\Number;
use Laravolt\Suitable\Columns\RestfulButton;
use Laravolt\Suitable\Columns\RowNumber;
use Laravolt\Suitable\Columns\Text;
use Laravolt\Ui\TableView;

final class ProductTable extends TableView
{
    public function data()
    {
        $me = auth()->user();
        $query = Product::query()->where('user_id', $me->id);

        if ($this->sortPayload()['sort']) {
            $query = $query->autoSort($this->sortPayload());
        } else {
            $query = $query->orderBy('name', 'asc');
        }

        $search = $this->search;
        if ($search) {
            $query->whereLike(['name', 'type'], $search);
        }

        return $query;
    }

    public function columns(): array
    {
        $only = ['show'];
        if (auth()->user()->can(Permission::PRODUCT_MANAGE)) {
            $only = array_merge($only, (['edit', 'destroy']));
        }

        return [
            RowNumber::make('#'),
            Text::make('name', 'Nama')->sortable(),
            Text::make('type', 'Tipe')->sortable(),
            Number::make('price', 'Harga')->sortable(),
            Number::make('weight', 'Berat (kg)')->sortable(),
            Number::make('length', 'Panjang (cm)')->sortable(),
            Number::make('width', 'Lebar (cm)')->sortable(),
            Number::make('height', 'Tinggi (cm)')->sortable(),
            RestfulButton::make('product')->only($only),
        ];
    }
}
