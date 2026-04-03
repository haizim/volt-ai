<x-volt-app title="Products">
    @slot('actions')
        <x-volt-link-button url="{{ route('product.create') }}" icon="plus" label="New" />
    @endslot

    @livewire(\App\Livewire\Table\ProductTable::class)
</x-volt-app>
