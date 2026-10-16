<x-volt-panel title="Labels">

    <h4 class="ui horizontal divider section">Solid Labels</h4>
    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-volt-label :color="'solid '.$color">{{ $color }}</x-volt-label>
            </div>
        @endforeach
    </div>

    <h4 class="ui horizontal divider section">Outline Labels</h4>
    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-volt-label :color="$color">{{ $color }}</x-volt-label>
            </div>
        @endforeach
    </div>

    <h4 class="ui horizontal divider section">Basic Labels</h4>
    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-volt-label :color="'basic '.$color">{{ $color }}</x-volt-label>
            </div>
        @endforeach
    </div>

    <h4 class="ui horizontal divider section">Circular Empty</h4>
    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-volt-label :color="'circular empty '.$color"></x-volt-label>
            </div>
        @endforeach
    </div>

</x-volt-panel>
