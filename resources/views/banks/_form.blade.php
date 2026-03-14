{!! form()->text('name')->label('Nama Bank')->placeholder('Masukkan nama bank') !!}
{!! form()->text('code')->label('Kode Bank')->placeholder('Contoh: BCA, BNI, BRI')->maxlength(10) !!}

<x-volt-link-button class="basic" url="{!! url()->previous() !!}" icon="arrow left">
    Kembali
</x-volt-link-button>
<x-volt-button>
    Simpan
</x-volt-button>