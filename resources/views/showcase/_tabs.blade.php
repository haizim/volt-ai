<x-volt-panel title="Tabs">
    <x-volt-tab>
        <x-volt-tab-content title="Overview" active>
            <p>This is the first tab panel. It is active by default using the <code>active</code> attribute.</p>
            <p>Tabs are great for organizing related content into sections without leaving the page.</p>
        </x-volt-tab-content>
        <x-volt-tab-content title="Details">
            <p>This is the second tab panel. It becomes active when the "Details" tab is clicked.</p>
            <ul class="ui list">
                <li>Detail item one</li>
                <li>Detail item two</li>
                <li>Detail item three</li>
            </ul>
        </x-volt-tab-content>
        <x-volt-tab-content title="History">
            <p>This is the third tab panel showing history or log information.</p>
            <div class="ui feed">
                <div class="event">
                    <div class="content">
                        <div class="summary">Record created on <strong>Jan 15, 2024</strong></div>
                    </div>
                </div>
                <div class="event">
                    <div class="content">
                        <div class="summary">Record updated on <strong>Feb 20, 2024</strong></div>
                    </div>
                </div>
            </div>
        </x-volt-tab-content>
    </x-volt-tab>
</x-volt-panel>
