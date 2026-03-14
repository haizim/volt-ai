<?php

declare(strict_types=1);

namespace App\Http\Livewire\Table;

use App\Models\Bank;
use Laravolt\Suitable\Columns\RestfulButton;
use Laravolt\Suitable\Columns\Text;
use Laravolt\Ui\TableView;

final class BankTable extends TableView
{
    public function data()
    {
        $query = Bank::query();
        $query->autoSort($this->sortPayload());

        return $query;
    }

    public function columns(): array
    {
        return [
            Text::make('id', 'ID')->sortable(),
            Text::make('name', 'Nama Bank')->sortable(),
            Text::make('code', 'Kode Bank')->sortable(),
            RestfulButton::make('banks'),
        ];
    }
}
