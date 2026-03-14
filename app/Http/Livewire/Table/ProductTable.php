<?php

declare(strict_types=1);

namespace App\Http\Livewire\Table;

use App\Enums\Permission;
use App\Models\Product;
use Laravolt\Suitable\Columns\Number;
use Laravolt\Suitable\Columns\RestfulButton;
use Laravolt\Suitable\Columns\Text;
use Laravolt\Ui\TableView;

final class ProductTable extends TableView
{
    public function data()
    {
        return Product::query()->autoSort($this->sortPayload());
    }

    public function columns(): array
    {
        $only = ['show'];

        if (auth()->user()->can(Permission::PRODUCT_MANAGE)) {
            $only = ['show', 'edit', 'destroy'];
        }
        return [
            Text::make('name', 'Nama Produk')->sortable(),
            Text::make('type', 'Tipe')->sortable(),
            Number::make('price', 'Harga')->sortable(),
            Number::make('weight', 'Berat')->sortable(),
            RestfulButton::make('products')->only($only),
        ];
    }
}
