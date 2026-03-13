<x-volt-app title="Outlet">
    <x-slot name="actions">
    @if (auth()->user()->can(\App\Enums\Permission::OUTLET_MANAGE))
        <x-volt-link-button
            :url="route('outlet.create')"
            icon="plus"
            label="Tambah"
        />
    @endif
    </x-slot>

    @livewire(\App\Http\Livewire\Table\OutletTable::class)
</x-volt-app>