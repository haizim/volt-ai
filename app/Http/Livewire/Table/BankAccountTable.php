<?php

declare(strict_types=1);

namespace App\Http\Livewire\Table;

use App\Models\BankAccount;
use Laravolt\Suitable\Columns\DateTime;
use Laravolt\Suitable\Columns\RestfulButton;
use Laravolt\Suitable\Columns\Text;
use Laravolt\Ui\TableView;

final class BankAccountTable extends TableView
{
    public function data()
    {
        $query = BankAccount::query()->with('bank');

        return $query->autoSort($this->sortPayload());
    }

    public function columns(): array
    {
        return [
            Text::make('bank.name', 'Bank')->sortable(),
            Text::make('account_number', 'Account Number')->sortable(),
            Text::make('by_name', 'Account Name')->sortable(),
            DateTime::make('created_at', 'Created At')->sortable(),
            RestfulButton::make('bank-accounts'),
        ];
    }
}
