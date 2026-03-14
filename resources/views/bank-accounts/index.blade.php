<x-volt-app title="Bank Accounts">
    <x-slot:actions>
        <x-volt-link-button 
            url="{{ route('bank-accounts.create') }}"
            icon="plus"
            label="Tambah"
        />
    </x-slot:actions>

    <div class="ui styled fluid accordion" id="accordion">
        <div class="title">
            <i class="dropdown icon"></i>
            Export
        </div>
        <div class=" content">
            <p>
                {!! form()->get()->action(route('bank-accounts.export')) !!}
                @php
                    $col = "column m-b-1";
                    $queryBank = 'SELECT id, name from banks';
                @endphp
                <div class="ui grid">
                    <div class="two column row">
                        <div class="{{ $col }}">
                            {!! form()->dropdownDB('bank_id', $queryBank, 'id', 'name')
                                ->label('Bank')
                                ->placeholder('-- Select Bank --') !!}
                        </div>
                    </div>
                </div>
                <x-volt-button class="m-t-2">
                    Export
                </x-volt-button>
                
                {!! form()->close() !!}
            </p>
        </div>
    </div>

    @livewire(\App\Http\Livewire\Table\BankAccountTable::class)

    <script>
        $('.ui.accordion').accordion();
    </script>
</x-volt-app>