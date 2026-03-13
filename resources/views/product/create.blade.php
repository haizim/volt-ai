<x-volt-app title="Tambah Produk">
    <x-volt-panel title="Tambah" icon="cube">
        {!! form()->open()->post()->action(route('product.store')) !!}
            
        @include('product._form')
            
        {!! form()->close() !!}
    </x-volt-panel>
</x-volt-app>