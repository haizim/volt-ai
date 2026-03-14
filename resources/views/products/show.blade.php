<x-volt-app title="Detail Produk">
    <x-slot:actions>
        <x-volt-link-button
            url="{{ route('products.index') }}"
            icon="arrow left"
            label="Kembali"
            class="secondary"
        />
        @if(auth()->user()->can(\App\Enums\Permission::PRODUCT_MANAGE))
            <x-volt-link-button
                url="{{ route('products.edit', $product->id) }}"
                icon="edit"
                label="Edit"
                class="yellow"
            />
        @endif
    </x-slot>

    <x-volt-panel title="Informasi Produk">
        <table class="ui definition table">
            <tr>
                <td>Nama Produk</td>
                <td>{{ $product->name }}</td>
            </tr>
            <tr>
                <td>Tipe</td>
                <td>{{ $product->type }}</td>
            </tr>
            <tr>
                <td>Harga</td>
                <td>{{ number_format($product->price, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Berat</td>
                <td>{{ number_format($product->weight, 0, ',', '.') }} gram</td>
            </tr>
        </table>
    </x-volt-panel>
</x-volt-app>
