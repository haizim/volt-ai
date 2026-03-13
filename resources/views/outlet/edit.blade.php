<x-volt-app title="Edit Outlet">
    <x-volt-panel title="Edit" icon="building">
        {!! form()->bind($outlet)->put()->action(route('outlet.update', $outlet->id)) !!}

        @include('outlet._form')

        {!! form()->close() !!}
    </x-volt-panel>
</x-volt-app>