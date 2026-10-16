<?php

declare(strict_types=1);

namespace App\Http\Livewire\Table;

use Laravolt\Suitable\Columns\Date;
use Laravolt\Suitable\Columns\Label;
use Laravolt\Suitable\Columns\Raw;
use Laravolt\Suitable\Columns\RowNumber;
use Laravolt\Suitable\Columns\Text;
use Laravolt\Ui\TableView;

final class ShowcaseTable extends TableView
{
    public function data(): array
    {
        return [
            ['id' => 1, 'name' => 'Alice Wonderland', 'email' => 'alice@example.com', 'status' => 'active', 'created_at' => '2024-01-15'],
            ['id' => 2, 'name' => 'Bob Builder', 'email' => 'bob@example.com', 'status' => 'banned', 'created_at' => '2024-02-20'],
            ['id' => 3, 'name' => 'Charlie Chaplin', 'email' => 'charlie@example.com', 'status' => 'active', 'created_at' => '2024-03-10'],
            ['id' => 4, 'name' => 'Diana Prince', 'email' => 'diana@example.com', 'status' => 'active', 'created_at' => '2024-04-05'],
            ['id' => 5, 'name' => 'Edward Norton', 'email' => 'edward@example.com', 'status' => 'banned', 'created_at' => '2024-05-22'],
        ];
    }

    public function columns(): array
    {
        return [
            RowNumber::make(),
            Text::make('name', 'Full Name')->sortable(),
            Text::make('email', 'Email'),
            Label::make('status')->map(['active' => 'green', 'banned' => 'red']),
            Date::make('created_at', 'Joined')->timezone(config('app.timezone')),
            Raw::make(fn ($row) => strtoupper($row['name']), 'UPPER NAME'),
            Raw::make(fn ($row) => '<a href="#" class="ui mini button blue">View</a> <a href="#" class="ui mini button basic">Edit</a>', 'Actions'),
        ];
    }
}
