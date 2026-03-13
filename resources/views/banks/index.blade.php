<x-volt-app title="Daftar Bank">
    <x-slot:actions>
        @if(auth()->user()->can(\App\Enums\Permission::BANK_MANAGE))
            <x-volt-link-button
                url="{{ route('banks.create') }}"
                icon="plus"
                label="Tambah Bank"
                class="primary"
            />
        @endif
    </x-slot>

    @livewire(\App\Http\Livewire\Table\BankTable::class)
</x-volt-app>
