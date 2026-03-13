{!! form()->text('name')->label('Nama')->required() !!}
{!! form()->textarea('address')->label('Alamat')->required() !!}
{!! form()->text('city')->label('Kota')->required() !!}
{!! form()->text('province')->label('Provinsi')->required() !!}
{!! form()->text('postal_code')->label('Kode Pos')->required() !!}

<x-volt-link-button class="basic" url="{!! url()->previous() !!}" icon="arrow left">
    Kembali
</x-volt-link-button>
<x-volt-button>
    Simpan
</x-volt-button>