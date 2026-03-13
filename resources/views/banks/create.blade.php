<x-volt-app title="Tambah Bank">
    <x-slot:actions>
        <x-volt-link-button
            url="{{ route('banks.index') }}"
            icon="arrow left"
            label="Kembali"
            class="secondary"
        />
    </x-slot>

    <x-volt-panel title="Tambah Bank Baru">
        {!! form()->open()->route('banks.store') !!}
        
        @include('banks._form')

        {!! form()->close() !!}
    </x-volt-panel>
</x-volt-app>
