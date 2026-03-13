<x-volt-app title="Tambah Kontak">
    <x-volt-panel title="Tambah" icon="users">
        {!! form()->open()->post()->action(route('contact.store')) !!}
            
        @include('contact._form')
            
        {!! form()->close() !!}
    </x-volt-panel>
</x-volt-app>
