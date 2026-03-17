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

    <div class="ui styled fluid accordion" id="accordion">
        <div class="title"><i class="dropdown icon"></i>Export Data Produk</div>
        <div class="content">
            {!! form()->open()->get()->action(route('products.export')) !!}
            <x-volt-button icon="download">Export</x-volt-button>
            {!! form()->close() !!}
        </div>
        @if(auth()->user()->can(\App\Enums\Permission::PRODUCT_MANAGE))
        <div class="title"><i class="dropdown icon"></i>Import</div>
        <div class="content">
            <p>
                {!! form()->open()->post()->action(route('products.import'))->id('import')->multipart() !!}
                {!! form()->file('file')->label('File')->required() !!}
                <x-volt-link-button class="secondary" url="{{ route('products.template') }}" icon="file excel">
                    Template
                </x-volt-link-button>
                <x-volt-button type="submit">
                    Import
                </x-volt-button>
                {!! form()->close() !!}
            </p>
        </div>
        @endif
    </div>

    @livewire(\App\Http\Livewire\Table\ProductTable::class)

    <script>$('.ui.accordion').accordion();</script>
</x-volt-app>
