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

    <div class="ui styled fluid accordion" id="accordion">
        <div class="title"><i class="dropdown icon"></i>Export Data Bank</div>
        <div class="content">
            {!! form()->open()->get()->action(route('banks.export')) !!}
            <x-volt-button icon="download">Export</x-volt-button>
            {!! form()->close() !!}
        </div>
    </div>
    
    @livewire(\App\Http\Livewire\Table\BankTable::class)
    
    <script>$('.ui.accordion').accordion();</script>
</x-volt-app>
