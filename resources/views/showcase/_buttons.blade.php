<x-volt-panel title="Buttons">

    <h4 class="ui horizontal divider section">Basic Buttons</h4>
    <div>
        <x-volt-button label="Primary Button" class="primary"></x-volt-button>
        <x-volt-button label="Secondary Button" class="secondary"></x-volt-button>
        <x-volt-button label="Basic Button" class="basic"></x-volt-button>
    </div>

    <h4 class="ui horizontal divider section">Color Variants</h4>
    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-volt-button class="{{ $color }}">{{ $color }}</x-volt-button>
            </div>
        @endforeach
    </div>

    <h4 class="ui horizontal divider section">Secondary Color Variants</h4>
    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-volt-button class="{{ $color }} secondary">{{ $color }}</x-volt-button>
            </div>
        @endforeach
    </div>

    <h4 class="ui horizontal divider section">Basic Color Variants</h4>
    <div class="ui horizontal list">
        @foreach(config('laravolt.ui.colors') as $color => $hex)
            <div class="item">
                <x-volt-button class="{{ $color }} basic">{{ $color }}</x-volt-button>
            </div>
        @endforeach
    </div>

    <h4 class="ui horizontal divider section">Link Button</h4>
    <x-volt-link-button url="#" icon="edit" label="Edit" class="blue" />
    <x-volt-link-button url="#" icon="eye" label="View" class="teal" />
    <x-volt-link-button url="#" icon="trash" label="Delete" class="red" />

    <h4 class="ui horizontal divider section">Dropdown (Fomantic UI)</h4>
    <div class="ui dropdown button">
        <i class="settings icon"></i>
        Actions
        <i class="dropdown icon"></i>
        <div class="menu">
            <a href="#" class="item"><i class="edit icon"></i> Edit</a>
            <a href="#" class="item"><i class="copy icon"></i> Duplicate</a>
            <div class="divider"></div>
            <a href="#" class="item"><i class="trash icon"></i> Delete</a>
        </div>
    </div>

</x-volt-panel>
