<x-volt-app title="Laravolt Components Showcase">

    <x-slot name="actions">
        <x-volt-link-button url="{{ route('home') }}" icon="home" label="Back to Home" />
    </x-slot>

    @include('showcase._typography')
    <div class="ui divider hidden"></div>

    @include('showcase._buttons')
    <div class="ui divider hidden"></div>

    @include('showcase._icons_links')
    <div class="ui divider hidden"></div>

    @include('showcase._breadcrumb')
    <div class="ui divider hidden"></div>

    @include('showcase._panels')
    <div class="ui divider hidden"></div>

    @include('showcase._cards')
    <div class="ui divider hidden"></div>

    @include('showcase._tabs')
    <div class="ui divider hidden"></div>

    @include('showcase._labels')
    <div class="ui divider hidden"></div>

    @include('showcase._forms')
    <div class="ui divider hidden"></div>

    @include('showcase._media')
    <div class="ui divider hidden"></div>

    @include('showcase._table')
    <div class="ui divider hidden"></div>

    @include('showcase._statistic')
    <div class="ui divider hidden"></div>

    @include('showcase._chart')

</x-volt-app>
