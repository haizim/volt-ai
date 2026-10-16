<h2 class="ui header">Panels</h2>

<div class="ui grid equal width">
    <div class="column">
        <x-volt-panel title="Basic Panel">
            <div class="ui placeholder">
                <div class="image header">
                    <div class="line"></div>
                    <div class="line"></div>
                </div>
                <div class="paragraph">
                    <div class="line"></div>
                    <div class="line"></div>
                </div>
            </div>
        </x-volt-panel>
    </div>
    <div class="column">
        <x-volt-panel title="Panel With Icon" icon="rocket">
            <div class="ui placeholder">
                <div class="image header">
                    <div class="line"></div>
                    <div class="line"></div>
                </div>
                <div class="paragraph">
                    <div class="line"></div>
                    <div class="line"></div>
                </div>
            </div>
        </x-volt-panel>
    </div>
</div>

<div class="ui grid equal width">
    <div class="column">
        <x-volt-panel title="Panel With Footer">
            <div class="ui placeholder">
                <div class="paragraph">
                    <div class="line"></div>
                    <div class="line"></div>
                </div>
            </div>
            <x-slot name="footer">
                <small>Created at January 2024</small>
            </x-slot>
        </x-volt-panel>
    </div>
    <div class="column">
        <x-volt-panel title="Panel With Action">
            <div class="ui placeholder">
                <div class="paragraph">
                    <div class="line"></div>
                    <div class="line"></div>
                </div>
            </div>
            <x-slot name="action">
                <x-volt-link-button url="#" icon="edit" class="mini">Edit</x-volt-link-button>
            </x-slot>
            <x-slot name="footer">
                <small>Panel footer with action button</small>
            </x-slot>
        </x-volt-panel>
    </div>
</div>
