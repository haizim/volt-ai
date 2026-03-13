<x-volt-app title="Edit Kontak">
    <x-volt-panel title="Edit" icon="users">
        {!! form()->bind($contact)->put()->action(route('contact.update', $contact->id)) !!}
            
        @include('contact._form')
            
        {!! form()->close() !!}
    </x-volt-panel>
</x-volt-app>