# Export Feature Workflow Guidelines

**Two-commit approach:** Implementation → Testing

**Workflow:** Read docs → Implement → Test

## Phase 1: Documentation (User)

**File:** `docs/exports/{feature}.md`

```markdown
    # Export {Feature Name}

    ## File Name
    {feature_name}-{Dynamic Value}-{Ymd-His}.xlsx

    ## Params
    - {param_name} (type) - description

    ## Sheets

    ### {Sheet Name}

    #### Query
    ```sql
    select column1 alias1, column2 alias2
    from main_table t1 
        join related_table t2 on t1.id = t2.foreign_key 
    where t.condition = {$paramName}
    order by t.sort_column asc
    ```

    #### Columns
    - Column Header (database_column) : Type
    - Column Header (database_column) : Type
```

## Phase 2: Implementation (AI)

### 2.1 Export Class

**File:** `app/Exports/{Feature}Export.php`

```php
<?php
declare(strict_types=1);

namespace App\Exports;

use App\Models\{MainModel};
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

final class {Feature}Export implements FromQuery, ShouldAutoSize, WithColumnFormatting, WithHeadings, WithMapping, WithTitle
{
    private $paramId;

    public function __construct($paramId)
    {
        $this->paramId = $paramId;
    }

    public function query()
    {
        return {MainModel}::query()
            ->where('foreign_key_column', $this->paramId)
            ->with('relationship')
            ->orderBy('sort_column', 'asc');
    }

    public function map($data): array
    {
        return [
            $data->relationship->field,
            $data->field1,
            $data->date_field->format('d/m/Y H:i'),
        ];
    }

    public function headings(): array
    {
        return ['Column Header 1', 'Column Header 2', 'Column Header 3'];
    }

    public function columnFormats(): array
    {
        return ['C' => 'dd/mm/yyyy h:mm'];
    }

    public function title(): string
    {
        return '{Sheet Name}';
    }
}
```

### 2.2 Service Layer

**File:** `app/Services/{Feature}Service.php`

```php
use App\Exports\{Feature}Export;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

public static function export(int $paramId): BinaryFileResponse
{
    $now = date('Ymd-His');
    $entity = RelatedModel::findOrFail($paramId);
    $fileName = "{feature}-{$entity->name}-{$now}.xlsx";
    
    return Excel::download(new {Feature}Export($paramId), $fileName);
}
```

### 2.3 Controller

**File:** `app/Http/Controllers/{Feature}Controller.php`

```php
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

public function export(Request $request): BinaryFileResponse
{
    Gate::authorize(Permission::{FEATURE}_VIEW);
    return {Feature}Service::export((int) $request->param_id);
}
```

### 2.4 UI & Routes

**UI:** `resources/views/{feature}/index.blade.php`

```blade
<div class="ui styled fluid accordion" id="accordion">
    <div class="title"><i class="dropdown icon"></i>Export</div>
    <div class="content">
        {!! form()->get()->action(route('{feature}.export')) !!}
        @php $queryRelated = 'SELECT id, name from related_table'; @endphp
        {!! form()->dropdownDB('param_id', $queryRelated, 'id', 'name')
            ->label('Parameter')->placeholder('-- Select --') !!}
        <x-volt-button>Export</x-volt-button>
        {!! form()->close() !!}
    </div>
</div>
<script>$('.ui.accordion').accordion();</script>
```

**Routes:** `routes/web.php`

```php
Route::middleware({Feature}View::class)->group(function (): void {
    Route::get('{feature}/export', [{Feature}Controller::class, 'export'])->name('{feature}.export');
    Route::resource('{feature}', {Feature}Controller::class);
});
```

## Phase 3: Testing (AI)

### 3.1 Feature Tests

**File:** `tests/Feature/{Feature}/{Feature}ControllerTest.php`

```php
test('it_can_export_{feature}_with_view_permission', function () {
    $entity = RelatedModel::factory()->create();
    Model::factory()->count(3)->create(['foreign_key' => $entity->id]);

    $response = $this->actingAs($this->normalUser)
        ->get(route('{feature}.export', ['param_id' => $entity->id]));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});

test('it_cannot_export_{feature}_without_view_permission', function () {
    $entity = RelatedModel::factory()->create();
    $userWithoutPermission = User::factory()->create();

    $response = $this->actingAs($userWithoutPermission)
        ->get(route('{feature}.export', ['param_id' => $entity->id]));

    $response->assertStatus(401);
});
```

### 3.2 Unit Tests

**File:** `tests/Unit/Services/{Feature}ServiceTest.php`

```php
use Symfony\Component\HttpFoundation\BinaryFileResponse;

test('it_can_export_{feature}_by_param_id', function () {
    $entity = RelatedModel::factory()->create(['name' => 'Test']);
    Model::factory()->count(3)->create(['foreign_key' => $entity->id]);

    $response = Service::export($entity->id);

    expect($response)->toBeInstanceOf(BinaryFileResponse::class);
    expect($response->getFile()->getExtension())->toBe('xlsx');
});

test('it_throws_exception_when_exporting_non_existent_entity', function () {
    Service::export(999);
})->throws(ModelNotFoundException::class);
```

## Checklist

**Documentation:**
- [ ] Read `docs/exports/{feature}.md` with SQL query and column mapping

**Implementation:**
- [ ] Create `app/Exports/{Feature}Export.php`
- [ ] Add export method to service
- [ ] Add export method to controller
- [ ] Add export form to index view
- [ ] Add export route

**Testing:**
- [ ] Add feature tests (success + permission denied)
- [ ] Add unit tests (export + exception)
- [ ] Run tests

## Common Issues

**Route conflicts:** Place export route before resource route

**Required imports:**
```php
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
```

**Dependency:** `composer require maatwebsite/excel`