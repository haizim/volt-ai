<x-volt-app title="Kontak">
    <x-slot name="actions">
    @if (auth()->user()->can(\App\Enums\Permission::CONTACT_MANAGE))
        <x-volt-link-button
            :url="route('contact.create')"
            icon="plus"
            label="Tambah"
        />
    @endif
    </x-slot>

    @livewire(\App\Http\Livewire\Table\ContactTable::class)
</x-volt-app>
