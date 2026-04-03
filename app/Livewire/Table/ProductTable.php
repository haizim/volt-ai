<?php

declare(strict_types=1);

namespace App\Livewire\Table;

use App\Models\Product;
use Laravolt\Suitable\Columns\Date;
use Laravolt\Suitable\Columns\Number;
use Laravolt\Suitable\Columns\RestfulButton;
use Laravolt\Suitable\Columns\Text;
use Laravolt\Ui\TableView;

final class ProductTable extends TableView
{
    public function data()
    {
        $query = Product::query();
        $query->autoSort($this->sortPayload());

        return $query;
    }

    public function columns(): array
    {
        return [
            Text::make('name', 'Name')->sortable(),
            Text::make('type', 'Type')->sortable(),
            Number::make('price', 'Price')->sortable(),
            Number::make('weight', 'Weight')->sortable(),
            Date::make('created_at', 'Created At')->sortable(),
            RestfulButton::make('product'),
        ];
    }
}
