<x-volt-app title="Detail Outlet">

    <x-volt-panel title="Detail" icon="building">

        <div class="ui grid m-b-1">
            <div class="two wide column"><b>Nama</b></div>
            <div class="fourteen wide column">: {{ $outlet->name }}</div>

            <div class="two wide column"><b>Alamat</b></div>
            <div class="fourteen wide column">: {{ $outlet->address }}</div>

            <div class="two wide column"><b>Kota</b></div>
            <div class="fourteen wide column">: {{ $outlet->city }}</div>

            <div class="two wide column"><b>Provinsi</b></div>
            <div class="fourteen wide column">: {{ $outlet->province }}</div>

            <div class="two wide column"><b>Kode Pos</b></div>
            <div class="fourteen wide column">: {{ $outlet->postal_code }}</div>
        </div>

        <x-volt-link-button class="basic" url="{!! url()->previous() !!}" icon="arrow left">
            Kembali
        </x-volt-link-button>

    </x-volt-panel>

</x-volt-app>