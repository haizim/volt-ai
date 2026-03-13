<x-volt-app title="Edit Bank">
    <x-slot:actions>
        <x-volt-link-button
            url="{{ route('banks.index') }}"
            icon="arrow left"
            label="Kembali"
            class="secondary"
        />
    </x-slot>

    <x-volt-panel title="Edit Bank">
        {!! form()->bind($bank)->put()->route('banks.update', $bank->id) !!}
        
        @include('banks._form')

        {!! form()->close() !!}
    </x-volt-panel>
</x-volt-app>
