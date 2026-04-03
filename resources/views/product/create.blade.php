<x-volt-app title="Create Product">
    <x-volt-panel title="Form">
        {!! form()->open()->post()->action(route('product.store')) !!}
            @include('product._form')

            <x-volt-link-button url="{{ route('product.index') }}" icon="arrow left" label="Cancel" class="secondary" />
            <x-volt-button icon="save" label="Save" />
        {!! form()->close() !!}
    </x-volt-panel>
</x-volt-app>
