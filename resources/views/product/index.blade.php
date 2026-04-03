<x-volt-app title="Products">
    @slot('actions')
        <x-volt-link-button url="{{ route('product.create') }}" icon="plus" label="New" />
    @endslot

    <div class="ui styled fluid accordion" id="accordion">
        @if (auth()->user()->can(\App\Enums\Permission::PRODUCT_MANAGE))
        <div class="title">
            <i class="dropdown icon"></i>
            Import
        </div>
        <div class="content">
            <p>
                {!! form()->open()->post()->action(route('product.import'))->id('import')->multipart() !!}
                {!! form()->file('file')->label('File')->required() !!}
                <x-volt-link-button class="secondary" url="{{ route('product.template') }}" icon="file excel">
                    Template
                </x-volt-link-button>
                <x-volt-button type="submit">
                    Import
                </x-volt-button>
                {!! form()->close() !!}
            </p>
        </div>
        @endif

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
