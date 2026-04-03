<x-volt-app title="Edit Product">
    <x-volt-panel title="Form">
        {!! form()->bind($product)->put()->action(route('product.update', $product->id)) !!}
            @include('product._form')

            <x-volt-link-button url="{{ route('product.index') }}" icon="arrow left" label="Cancel" class="secondary" />
            <x-volt-button icon="save" label="Save" />
        {!! form()->close() !!}
    </x-volt-panel>
</x-volt-app>
