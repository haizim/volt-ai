<x-volt-app title="Products">
    @slot('actions')
        <x-volt-link-button url="{{ route('product.create') }}" icon="plus" label="New" />
    @endslot

    <div class="ui styled fluid accordion" id="accordion">
        <div class="title"><i class="dropdown icon"></i>Export</div>
        <div class="content">
            {!! form()->get()->action(route('product.export')) !!}
            <x-volt-button icon="download" label="Export" />
            {!! form()->close() !!}
        </div>
    </div>

    @livewire(\App\Livewire\Table\ProductTable::class)

    <script>
        $('.ui.accordion').accordion();
    </script>
</x-volt-app>
