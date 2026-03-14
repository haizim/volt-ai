<x-volt-app title="Edit Bank Account">
    <x-slot:actions>
        <x-volt-link-button 
            url="{{ route('bank-accounts.index') }}" 
            icon="arrow left" 
            label="Back to Bank Accounts" 
            class="grey" 
        />
    </x-slot:actions>

    <x-volt-panel title="Bank Account Details">
        {!! form()->bind($bankAccount)->put()->action(route('bank-accounts.update', $bankAccount->id)) !!}
            @include('bank-accounts._form')
        {!! form()->close() !!}
    </x-volt-panel>
</x-volt-app>