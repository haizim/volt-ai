<x-volt-app title="Produk">
    <x-slot name="actions">
    @if (auth()->user()->can(\App\Enums\Permission::PRODUCT_MANAGE))
        <x-volt-link-button
            :url="route('product.create')"
            icon="plus"
            label="Tambah"
        />
    @endif
    </x-slot>

    @livewire(\App\Http\Livewire\Table\ProductTable::class)
</x-volt-app>