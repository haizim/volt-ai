<x-volt-app title="Create Bank Account">
    <x-slot:actions>
        <x-volt-link-button 
            url="{{ route('bank-accounts.index') }}" 
            icon="arrow left" 
            label="Back to Bank Accounts" 
            class="grey" 
        />
    </x-slot:actions>

    <x-volt-panel title="Bank Account Details">
        {!! form()->open()->post()->action(route('bank-accounts.store')) !!}
            @include('bank-accounts._form')
        {!! form()->close() !!}
    </x-volt-panel>
</x-volt-app>