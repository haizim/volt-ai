{!! form()->text('name')->label('Nama')->required() !!}
{!! form()->text('type')->label('Tipe')->required() !!}
{!! form()->number('price')->label('Harga')->min(0)->step(1)->required() !!}
{!! form()->number('weight')->label('Berat (kg)')->min(0)->step(0.01)->required() !!}
{!! form()->number('length')->label('Panjang (cm)')->min(0)->step(0.01)->required() !!}
{!! form()->number('width')->label('Lebar (cm)')->min(0)->step(0.01)->required() !!}
{!! form()->number('height')->label('Tinggi (cm)')->min(0)->step(0.01)->required() !!}

<x-volt-link-button class="basic" url="{!! url()->previous() !!}" icon="arrow left">
    Kembali
</x-volt-link-button>
<x-volt-button>
    Simpan
</x-volt-button>