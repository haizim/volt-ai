<x-volt-app title="Daftar Produk">
    <x-slot:actions>
        @if(auth()->user()->can(\App\Enums\Permission::PRODUCT_MANAGE))
            <x-volt-link-button
                url="{{ route('products.create') }}"
                icon="plus"
                label="Tambah Produk"
                class="primary"
            />
        @endif
    </x-slot>

    @livewire(\App\Http\Livewire\Table\ProductTable::class)
</x-volt-app>
