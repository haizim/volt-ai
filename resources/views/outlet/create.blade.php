<x-volt-app title="Tambah Outlet">
    <x-volt-panel title="Tambah" icon="building">
        {!! form()->open()->post()->action(route('outlet.store')) !!}

        @include('outlet._form')

        {!! form()->close() !!}
    </x-volt-panel>
</x-volt-app>