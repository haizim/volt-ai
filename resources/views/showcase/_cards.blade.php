<x-volt-panel title="Cards">
    <x-volt-cards>
        <x-volt-card
            meta.before="<i class='icon star yellow'></i> Featured"
            title="Basic Card with Meta"
            content="A simple card with title, content text, and meta information."
            url="#"
        >
            <x-volt-card-footer left='<i class="icon check"></i> 42 votes' right="Jan 2024" />
        </x-volt-card>

        <x-volt-card
            meta.before="<i class='icon coins yellow'></i> Premium"
            title="Card with Long Content"
            content="Lorem ipsum dolor sit amet, consectetur adipisicing elit. Asperiores atque culpa distinctio facilis minima neque quas. Consequatur delectus distinctio ducimus expedita minus mollitia."
        >
            <x-volt-card-footer left='<i class="icon heart"></i> 99 likes' right="Feb 2024" />
        </x-volt-card>

        <x-volt-card
            meta.before="<i class='icon tag blue'></i> Tutorial"
            title="Card with Labels"
            content="This card showcases the label component inside the card body slot."
        >
            <x-slot name="body">
                <div class="content">
                    <div class="description">
                        <x-volt-label label="Laravel" color="theme solid"></x-volt-label>
                        <x-volt-label label="Livewire" color="theme secondary"></x-volt-label>
                    </div>
                </div>
            </x-slot>
            <x-volt-card-footer left='<i class="icon bookmark"></i> Saved' right="Mar 2024" />
        </x-volt-card>
    </x-volt-cards>
</x-volt-panel>
