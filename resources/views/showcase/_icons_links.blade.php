<x-volt-panel title="Icons & Links">

    <h4 class="ui horizontal divider section">Icons (x-volt-icon)</h4>
    <div>
        <x-volt-icon name="user" />
        <x-volt-icon name="home" />
        <x-volt-icon name="cog" />
        <x-volt-icon name="heart" :size="24" />
        <x-volt-icon name="star" :size="24" />
        <x-volt-icon name="check-circle" :size="24" />
        <x-volt-icon name="times-circle" :size="24" />
        <x-volt-icon name="info-circle" :size="32" />
    </div>

    <h4 class="ui horizontal divider section">Links (x-volt-link)</h4>
    <div>
        <x-volt-link url="{{ route('home') }}" icon="home">Go to Home</x-volt-link>
        &nbsp;&nbsp;
        <x-volt-link url="{{ route('showcase') }}" icon="paint-brush">Showcase</x-volt-link>
    </div>

    <h4 class="ui horizontal divider section">Brand Image (x-volt-brand-image)</h4>
    <p class="ui message info">
        <i class="info circle icon"></i>
        <code>x-volt-brand-image</code> membaca gambar dari config <code>laravolt.ui.brand_image</code>, bukan dari atribut src.
    </p>
    <x-volt-brand-image />

</x-volt-panel>
