<x-volt-app title="Detail Produk">

    <x-volt-panel title="Detail" icon="cube">

        <div class="ui grid m-b-1">
            <div class="two wide column"><b>Nama</b></div>
            <div class="fourteen wide column">: {{ $product->name }}</div>

            <div class="two wide column"><b>Tipe</b></div>
            <div class="fourteen wide column">: {{ $product->type }}</div>

            <div class="two wide column"><b>Harga</b></div>
            <div class="fourteen wide column">: {{ number_format($product->price, 0, ',', '.') }}</div>

            <div class="two wide column"><b>Berat</b></div>
            <div class="fourteen wide column">: {{ number_format($product->weight, 2, ',', '.') }} kg</div>

            <div class="two wide column"><b>Panjang</b></div>
            <div class="fourteen wide column">: {{ number_format($product->length, 2, ',', '.') }} cm</div>

            <div class="two wide column"><b>Lebar</b></div>
            <div class="fourteen wide column">: {{ number_format($product->width, 2, ',', '.') }} cm</div>

            <div class="two wide column"><b>Tinggi</b></div>
            <div class="fourteen wide column">: {{ number_format($product->height, 2, ',', '.') }} cm</div>
        </div>

        <x-volt-link-button class="basic" url="{!! url()->previous() !!}" icon="arrow left">
            Kembali
        </x-volt-link-button>

    </x-volt-panel>

</x-volt-app>