<x-volt-app title="Tambah Produk">
    <x-slot:actions>
        <x-volt-link-button
            url="{{ route('products.index') }}"
            icon="arrow left"
            label="Kembali"
            class="secondary"
        />
    </x-slot>

    <x-volt-panel title="Tambah">
        {!! form()->open()->post()->action(route('products.store')) !!}
            
            @include('products._form', ['product' => null])
            
        {!! form()->close() !!}
    </x-volt-panel>
</x-volt-app>
