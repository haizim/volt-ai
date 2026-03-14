<x-volt-app title="Bank Account Details">
    <x-slot:actions>
        <x-volt-link-button 
            url="{{ route('bank-accounts.edit', $bankAccount->id) }}" 
            icon="edit" 
            label="Edit" 
            class="warning"
        />
        <x-volt-link-button 
            url="{{ route('bank-accounts.index') }}" 
            icon="arrow left" 
            label="Kembali"
            class="secondary" 
        />
    </x-slot:actions>

    <x-volt-panel title="Details">
        <table class="ui very basic table">
            <tbody>
                <tr>
                    <td class="collapsing"><strong>Bank</strong></td>
                    <td>{{ $bankAccount->bank->name }}</td>
                </tr>
                <tr>
                    <td class="collapsing"><strong>Account Number</strong></td>
                    <td>{{ $bankAccount->account_number }}</td>
                </tr>
                <tr>
                    <td class="collapsing"><strong>Account Name</strong></td>
                    <td>{{ $bankAccount->by_name }}</td>
                </tr>
                <tr>
                    <td class="collapsing"><strong>Created At</strong></td>
                    <td>{{ $bankAccount->created_at->format('d M Y, H:i') }}</td>
                </tr>
                @if($bankAccount->updated_at->ne($bankAccount->created_at))
                <tr>
                    <td class="collapsing"><strong>Updated At</strong></td>
                    <td>{{ $bankAccount->updated_at->format('d M Y, H:i') }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </x-volt-panel>
</x-volt-app>