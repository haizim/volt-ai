<x-volt-panel title="Form Fields — All Input Types">
    {!! form()->open(route('platform::dump'))->multipart() !!}

    <h4 class="ui horizontal divider section">Text Inputs</h4>
    {!! form()->text('name')->label('Text') !!}
    {!! form()->email('email')->label('Email') !!}
    {!! form()->password('password')->label('Password') !!}
    {!! form()->number('age', 25)->min(1)->max(100)->step(1)->label('Number') !!}
    {!! form()->color('color', '#3b82f6')->label('Color Picker') !!}
    {!! form()->hidden('_token_hidden', 'example-value') !!}

    <h4 class="ui horizontal divider section">Text Area & WYSIWYG</h4>
    {!! form()->textarea('description')->label('Textarea') !!}
    {!! form()->redactor('content')->label('WYSIWYG (Redactor)') !!}

    <h4 class="ui horizontal divider section">Currency</h4>
    {!! form()->rupiah('price', 150000)->label('Rupiah') !!}

    <h4 class="ui horizontal divider section">Checkbox & Radio</h4>
    {!! form()->checkbox('remember', 1, true)->label('Remember Me') !!}
    {!! form()->radio('gender', 'male', true)->label('Male') !!}
    {!! form()->radio('gender', 'female')->label('Female') !!}
    {!! form()->checkboxGroup('fruits', ['apple' => 'Apple', 'banana' => 'Banana', 'cherry' => 'Cherry'], 'banana')->label('Checkbox Group') !!}
    {!! form()->radioGroup('size', ['sm' => 'Small', 'md' => 'Medium', 'lg' => 'Large'], 'md')->label('Radio Group') !!}

    <h4 class="ui horizontal divider section">Select & Dropdown</h4>
    {!! form()->select('country', ['id' => 'Indonesia', 'ms' => 'Malaysia', 'sg' => 'Singapore'])->label('Country')->placeholder('-- Select Country --') !!}
    {!! form()->select('tags', ['laravel' => 'Laravel', 'vue' => 'Vue', 'react' => 'React', 'alpine' => 'Alpine'])->multiple()->label('Tags (Multiple)') !!}
    {!! form()->selectRange('children', 0, 10)->label('Number of Children') !!}
    {!! form()->selectMonth('birth_month')->label('Birth Month') !!}
    {!! form()->dropdownDB('user', 'select id, email as name from users limit 10')->label('Search User (DB)') !!}

    <h4 class="ui horizontal divider section">Date & Time</h4>
    {!! form()->date('birthday')->label('Date') !!}
    {!! form()->time('start_time')->label('Time') !!}
    {!! form()->datepicker('event_date')->label('Datepicker') !!}
    {!! form()->timepicker('event_time')->label('Timepicker') !!}
    {!! form()->selectDate('myDate', 2000, 2030)->label('Select Date') !!}

    <h4 class="ui horizontal divider section">File Upload</h4>
    {!! form()->file('attachment')->label('File Input') !!}
    {!! form()->uploader('avatar')->limit(1)->extensions(['jpg', 'png', 'gif'])->fileMaxSize(5)->label('Single File Uploader') !!}
    {!! form()->uploader('documents')->limit(10)->extensions(['pdf', 'doc', 'docx'])->label('Multiple File Uploader') !!}

    <h4 class="ui horizontal divider section">Map Coordinate</h4>
    {!! form()->coordinate('location')->label('Coordinate (Map)') !!}

    <h4 class="ui horizontal divider section">Actions</h4>
    {!! form()->action(form()->submit('Save'), form()->button('Cancel')) !!}

    {!! form()->close() !!}
</x-volt-panel>

<div class="ui divider hidden"></div>

<x-volt-panel title="Form — Field Hints">
    {!! form()->get()->horizontal() !!}
    {!! form()->text('username')->label('Username')->hint('Minimal 6 karakter.')->hint('Hanya boleh huruf dan angka.') !!}
    {!! form()->password('pass')->label('Password')->hint('Minimal 8 karakter.')->hint('Harus mengandung huruf, angka, dan karakter spesial.') !!}
    {!! form()->close() !!}
</x-volt-panel>

<div class="ui divider hidden"></div>

<div class="ui grid equal width">
    <div class="column">
        <x-volt-panel title="Horizontal Form Layout">
            {!! form()->get()->horizontal() !!}
            <h3 class="ui horizontal divider section">Basic Info</h3>
            {!! form()->text('nama')->label('Nama') !!}
            {!! form()->select('lokasi', ['id' => 'Indonesia', 'ms' => 'Malaysia'])->label('Lokasi') !!}
            <h3 class="ui horizontal divider section">Preferences</h3>
            {!! form()->select('language', ['id' => 'Indonesia', 'en' => 'English'])->label('Language') !!}
            {!! form()->select('timezone', ['Asia/Jakarta' => 'WIB', 'Asia/Makassar' => 'WITA', 'Asia/Jayapura' => 'WIT'])->label('Timezone') !!}
            {!! form()->action(form()->submit('Simpan')) !!}
            {!! form()->close() !!}
        </x-volt-panel>
    </div>
    <div class="column">
        <x-volt-panel title="Vertical Form Layout">
            {!! form()->get() !!}
            <h3 class="ui horizontal divider section">Basic Info</h3>
            {!! form()->text('nama2')->label('Nama') !!}
            {!! form()->select('lokasi2', ['id' => 'Indonesia', 'ms' => 'Malaysia'])->label('Lokasi') !!}
            <h3 class="ui horizontal divider section">Preferences</h3>
            {!! form()->select('language2', ['id' => 'Indonesia', 'en' => 'English'])->label('Language') !!}
            {!! form()->select('timezone2', ['Asia/Jakarta' => 'WIB', 'Asia/Makassar' => 'WITA'])->label('Timezone') !!}
            {!! form()->submit('Simpan') !!}
            {!! form()->close() !!}
        </x-volt-panel>
    </div>
</div>
