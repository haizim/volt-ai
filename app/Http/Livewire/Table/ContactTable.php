<?php

namespace App\Http\Livewire\Table;

use App\Enums\Permission;
use App\Models\Contact;
use Laravolt\Suitable\Columns\RestfulButton;
use Laravolt\Suitable\Columns\RowNumber;
use Laravolt\Suitable\Columns\Text;
use Laravolt\Ui\TableView;

class ContactTable extends TableView
{
    public function data()
    {
        $me = auth()->user();
        $query = Contact::query()->where('user_id', $me->id);

        if ($this->sortPayload()['sort']) {
            $query = $query->autoSort($this->sortPayload());
        } else {
            $query = $query->orderBy('first_name', 'asc');
        }

        $search = $this->search;
        if ($search) {
            $query->whereLike(['first_name', 'last_name', 'email', 'phone'], $search);
        }

        return $query;
    }

    public function columns(): array
    {
        $only = ['show'];
        if (auth()->user()->can(Permission::CONTACT_MANAGE)) {
            $only = array_merge($only, (['edit', 'destroy']));
        }

        return [
            RowNumber::make('#'),
            Text::make('first_name', 'Nama Depan')->sortable(),
            Text::make('last_name', 'Nama Belakang')->sortable(),
            Text::make('email', 'Email')->sortable(),
            Text::make('phone', 'No. Telepon')->sortable(),
            RestfulButton::make('contact')->only($only),
        ];
    }
}
