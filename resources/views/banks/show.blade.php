<x-volt-app title="Detail Bank">
    <x-slot:actions>
        @if(auth()->user()->can(\App\Enums\Permission::BANK_MANAGE))
            <x-volt-link-button
                url="{{ route('banks.edit', $bank->id) }}"
                icon="edit"
                label="Edit"
                class="warning"
            />
        @endif
        
        <x-volt-link-button
            url="{{ route('banks.index') }}"
            icon="arrow left"
            label="Kembali"
            class="secondary"
        />
    </x-slot>

    <x-volt-panel title="Detail Bank">
        <table class="ui very basic table">
            <tbody>
                <tr>
                    <td class="collapsing">
                        <strong>ID</strong>
                    </td>
                    <td>{{ $bank->id }}</td>
                </tr>
                <tr>
                    <td class="collapsing">
                        <strong>Nama Bank</strong>
                    </td>
                    <td>{{ $bank->name }}</td>
                </tr>
                <tr>
                    <td class="collapsing">
                        <strong>Kode Bank</strong>
                    </td>
                    <td>{{ $bank->code }}</td>
                </tr>
                <tr>
                    <td class="collapsing">
                        <strong>Dibuat Pada</strong>
                    </td>
                    <td>{{ $bank->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td class="collapsing">
                        <strong>Diperbarui Pada</strong>
                    </td>
                    <td>{{ $bank->updated_at->format('d/m/Y H:i') }}</td>
                </tr>
            </tbody>
        </table>
    </x-volt-panel>
</x-volt-app>
