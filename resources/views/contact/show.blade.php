<x-volt-app title="Detail Kontak">

    <x-volt-panel title="Detail" icon="users">

        <div class="ui grid m-b-1">
            <div class="two wide column"><b>Nama Depan</b></div>
            <div class="fourteen wide column">: {{ $contact->first_name }}</div>

            <div class="two wide column"><b>Nama Belakang</b></div>
            <div class="fourteen wide column">: {{ $contact->last_name }}</div>

            <div class="two wide column"><b>Email</b></div>
            <div class="fourteen wide column">: {{ $contact->email }}</div>

            <div class="two wide column"><b>No. Telepon</b></div>
            <div class="fourteen wide column">: {{ $contact->phone }}</div>
        </div>

        <x-volt-link-button class="basic" url="{!! url()->previous() !!}" icon="arrow left">
            Kembali
        </x-volt-link-button>

    </x-volt-panel>

</x-volt-app>