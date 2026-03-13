{!! form()->text('first_name')->label('Nama Depan')->required() !!}
{!! form()->text('last_name')->label('Nama Belakang') !!}
{!! form()->email('email')->label('Email') !!}
{!! form()->text('phone')->label('No. Telepon')->required() !!}

<x-volt-link-button class="basic" url="{!! url()->previous() !!}" icon="arrow left">
    Kembali
</x-volt-link-button>
<x-volt-button>
    Simpan
</x-volt-button>