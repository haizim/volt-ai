<x-volt-app title="Bank Accounts">
    <x-slot:actions>
        <x-volt-link-button 
            url="{{ route('bank-accounts.create') }}"
            icon="plus"
            label="Tambah"
        />
    </x-slot:actions>

    @livewire(\App\Http\Livewire\Table\BankAccountTable::class)
</x-volt-app>