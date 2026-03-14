<x-volt-app title="Edit Produk">
    <x-slot:actions>
        <x-volt-link-button
            url="{{ route('products.index') }}"
            icon="arrow left"
            label="Kembali"
            class="secondary"
        />
    </x-slot>

    <x-volt-panel title="Edit">
        {!! form()->bind($product)->put()->action(route('products.update', $product->id)) !!}
            @include('products._form', ['product' => $product])
        {!! form()->close() !!}
    </x-volt-panel>
</x-volt-app>
