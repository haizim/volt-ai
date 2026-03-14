{!! form()->text('name', $product->name ?? null)->label('Nama Produk') !!}
{!! form()->text('type', $product->type ?? null)->label('Tipe') !!}
{!! form()->number('price', $product->price ?? null)->label('Harga')->min(0) !!}
{!! form()->number('weight', $product->weight ?? null)->label('Berat')->min(0) !!}

<x-volt-link-button class="basic" url="{!! url()->previous() !!}" icon="arrow left">
    Kembali
</x-volt-link-button>
<x-volt-button>
    Simpan
</x-volt-button>