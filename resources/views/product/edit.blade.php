<x-volt-app title="Edit Produk">
    <x-volt-panel title="Edit" icon="cube">
        {!! form()->bind($product)->put()->action(route('product.update', $product->id)) !!}
            
        @include('product._form')
            
        {!! form()->close() !!}
    </x-volt-panel>
</x-volt-app>