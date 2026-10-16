<x-volt-panel title="Breadcrumb">
    <p class="ui message info">
        <i class="info circle icon"></i>
        Komponen <code>x-volt-breadcrumb</code> me-render konten ke dalam <code>@section('breadcrumb')</code> di layout. Gunakan <code>x-volt-item</code> sebagai child-nya. Contoh di bawah menunjukkan struktur HTML breadcrumb Fomantic UI secara langsung.
    </p>
    <div class="ui breadcrumb">
        <a href="{{ route('home') }}" class="section">Home</a>
        <i class="right angle icon divider"></i>
        <a href="{{ route('showcase') }}" class="section">Components</a>
        <i class="right angle icon divider"></i>
        <div class="active section">Breadcrumb</div>
    </div>

    <div class="ui divider hidden"></div>

    <p>Penggunaan <code>x-volt-breadcrumb</code> dengan <code>x-volt-item</code>:</p>
    <x-volt-breadcrumb>
        <x-volt-item><a href="{{ route('home') }}" class="section">Home</a></x-volt-item>
        <x-volt-item><a href="{{ route('showcase') }}" class="section">Showcase</a></x-volt-item>
        <x-volt-item><span class="active section">Breadcrumb</span></x-volt-item>
    </x-volt-breadcrumb>
</x-volt-panel>
