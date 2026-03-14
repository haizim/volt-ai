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
    </div>

    @livewire(\App\Http\Livewire\Table\ProductTable::class)

    <script>$('.ui.accordion').accordion();</script>
</x-volt-app>
