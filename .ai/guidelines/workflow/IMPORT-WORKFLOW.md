# Import Feature Workflow Guidelines

Based on the established patterns in the Bank and Bank Account import implementations, these guidelines define the standard workflow for creating complete import features in the Laravolt ecosystem.

## Role Separation

**CRITICAL:** This workflow has clear role separation between USER and AI AGENT:

### USER Responsibilities:
- ✅ Create import documentation at `docs/imports/{resource}.md`
- ✅ Define model name, template structure, and validation rules
- ✅ Provide complete specification before AI implementation

### AI AGENT Responsibilities:
- ✅ Read and understand the user-created documentation
- ✅ Implement import feature based on documentation blueprint
- ✅ Create comprehensive tests for import functionality
- ❌ **NEVER create the import documentation**

**Workflow Rule:** Documentation is the SINGLE SOURCE OF TRUTH. AI agent implements EXACTLY what is specified in the documentation.

## Workflow Overview

The import feature workflow follows a **three-phase approach** with clear role separation:

1. **Documentation Phase (USER)** - User creates import specification document at `docs/imports/{resource}.md`
2. **Implementation Phase (AI AGENT)** - AI agent reads documentation and creates import classes, templates, services, controllers, routes, and views
3. **Testing Phase (AI AGENT)** - AI agent creates comprehensive test coverage for import functionality

**Critical Workflow Rule:**
- **USER** creates the documentation → AI agent reads and implements
- **AI agent NEVER creates the import documentation**
- **AI agent ONLY reads and uses the documentation** as reference for implementation

---

## Phase 1: Documentation Phase (User Step)

### 1.1 Create Import Documentation

**Step:** User creates `docs/imports/{resource}.md`

**IMPORTANT:** This documentation MUST be created by the USER before AI implementation begins. The AI agent will read and use this documentation as the blueprint for implementation.

The user should create a comprehensive import specification document following the structure below.

**Documentation Structure:**

```markdown
# Import {Resource Name}

## Model

{ModelName}

## Form

- File : file (file xlsx)

## Template

### Sheet : Import

#### Columns

- {Column Name 1} : {data type/reference}
- {Column Name 2} : {data type/reference}
- ...

## Import

### Rules

- {field_1} : {validation rules}
- {field_2} : {validation rules}
- ...

### Data Map

- {excel_column_1} : {model_field_1}
- {excel_column_2} : {model_field_2}
- ...
```

**IMPORTANT:** The **Data Map** section is CRITICAL for AI agents to understand how to map Excel column names to model field names.

**Examples:**

**Simple Import (Single Sheet):**
```markdown
# Import Bank

## Model

Bank

## Form

- File : file (file xlsx)

## Template

### Sheet : Import

#### Columns

- Kode Bank : string
- Nama Bank : string

## Import

### Rules

- kode_bank : required|string|max:10
- nama_bank : required|string|max:255

### Data Map

- kode_bank : code
- nama_bank : name
```

**Complex Import (Multiple Sheets with References):**
```markdown
# Import Bank Accounts

## Model

BankAccount

## Form

- File : file (file xlsx)

## Template

### Sheet : Import

#### Columns

- Kode Bank : {bank->code}
- Nama Bank : =VLOOKUP(A2,Bank!A:B,2,FALSE)
- Nomor Rekening : random_int(100000000, 999999999)
- Atas Nama : fake()->name

### Sheet : Bank

#### Model

Bank

#### Columns

- Kode Bank : string
- Nama Bank : string

## Import

### Rules

- kode_bank : required|string|exists:banks,code
- nama_bank : nullable|string
- nomor_rekening : required|numeric
- atas_nama : required|string|max:255

### Data Map

- kode_bank : get bank's id by bank's code
- nomor_rekening : account_number
- atas_nama : by_name
```

**Documentation Conventions:**
- Use kebab-case for filename: `{resource}.md`
- Use singular PascalCase for model names
- Specify column data types: `string`, `numeric`, `date`, etc.
- For referenced columns: use `{model->field}` notation
- For Excel formulas: use actual Excel syntax
- For example values: use PHP function names as hints
- **Data Map section is REQUIRED** - maps Excel columns to model fields
  - Direct mapping: `excel_column : model_field`
  - Reference lookup: `excel_column : get related's id by related's field`
  - Skip non-model fields (display-only): omit from Data Map

---

## Phase 2: Implementation Phase (AI Agent)

### AI Agent First Step: Read Documentation

**CRITICAL:** When user requests import feature implementation, the AI agent MUST:

1. **Verify documentation exists:**
   ```bash
   # Check if user has created the documentation
   ls docs/imports/{resource}.md
   ```

2. **If documentation does NOT exist:**
   ```
   STOP and ask user to create the documentation first:
   "I need you to create the import documentation at docs/imports/{resource}.md
    before I can implement the import feature. Please follow the structure
    defined in Phase 1 of the workflow guidelines."
   ```

3. **If documentation exists:**
   ```bash
   # Read the user-created documentation
   Read docs/imports/{resource}.md
   ```

4. **Verify documentation is complete:**
   - Check that `docs/imports/{resource}.md` exists
   - Verify all required sections are present:
     - Model name
     - Form specification
     - Template structure (columns, data types)
     - Import validation rules
     - **Data Map section (CRITICAL)**

5. **Use documentation as blueprint:**
   - Extract model name from documentation
   - Extract template columns and data types
   - Extract validation rules
   - **Extract Data Map to understand field mappings**
   - Follow the exact structure specified in documentation

**Understanding Data Map:**
The Data Map section tells AI agents how to map Excel columns to model fields:
- **Direct mapping:** `excel_column : model_field`
  - Example: `kode_bank : code` means Excel column "kode_bank" maps to model field "code"
- **Reference lookup:** `excel_column : get related's id by related's field`
  - Example: `kode_bank : get bank's id by bank's code` means look up Bank model by 'code' field and use its 'id'
- **Display-only columns:** Omitted from Data Map (not stored in database)

**AI Agent Workflow:**
```
User Request: "Create import feature for {resource}"
    ↓
AI Agent: Check if docs/imports/{resource}.md exists
    ↓ (if NO)
    → Ask user to create documentation first
    ↓ (if YES)
AI Agent: Read docs/imports/{resource}.md
    ↓
AI Agent: Extract requirements from documentation
    ↓
AI Agent: Implement based on documentation blueprint
    ↓
AI Agent: Create tests
```

### 2.1 Create Import Class

**File:** `app/Imports/{Resource}Import.php`

Create an import class implementing Laravel Excel interfaces.

**IMPORTANT:** Use the **Data Map** section from the documentation to map Excel columns to model fields.

**Simple Import Pattern (No External Dependencies):**

*Documentation Example:*
```markdown
### Data Map
- kode_bank : code
- nama_bank : name
```

*Implementation:*
```php
<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\{Resource};
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

final class {Resource}Import implements SkipsEmptyRows, ToCollection, WithHeadingRow, WithValidation
{
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            // Use Data Map from documentation:
            // kode_bank -> code
            // nama_bank -> name

            {Resource}::updateOrCreate(
                ['code' => $row['kode_bank']],
                [
                    'name' => $row['nama_bank'],
                    'code' => $row['kode_bank'],
                ]
            );
        }
    }

    public function rules(): array
    {
        return [
            // Use Excel column names from documentation
            'kode_bank' => 'required|string|max:255',
            'nama_bank' => 'required|string|max:255',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'kode_bank.required' => 'Column 1 is required',
            'kode_bank.max' => 'Column 1 must not exceed 255 characters',
            'nama_bank.required' => 'Column 2 is required',
        ];
    }
}
```

```php
<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\{Resource};
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

final class {Resource}Import implements SkipsEmptyRows, ToCollection, WithHeadingRow, WithValidation
{
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            {Resource}::updateOrCreate(
                ['unique_field' => $row['excel_column_name']],
                [
                    'field1' => $row['excel_column_1'],
                    'field2' => $row['excel_column_2'],
                    'unique_field' => $row['excel_column_name'],
                ]
            );
        }
    }

    public function rules(): array
    {
        return [
            'excel_column_1' => 'required|string|max:255',
            'excel_column_2' => 'required|string|max:255',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'excel_column_1.required' => 'Column 1 is required',
            'excel_column_1.max' => 'Column 1 must not exceed 255 characters',
            'excel_column_2.required' => 'Column 2 is required',
        ];
    }
}
```

**Complex Import Pattern (With External References & Error Handling):**

*Documentation Example:*
```markdown
### Data Map
- kode_bank : get bank's id by bank's code
- nomor_rekening : account_number
- atas_nama : by_name
```

*Implementation:*
```php
<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\Bank;
use App\Models\{Resource};
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithValidation;

final class {Resource}Import implements SkipsEmptyRows, ToCollection, WithHeadingRow, WithMultipleSheets, WithValidation
{
    /**
     * @var array<string>
     */
    protected array $errors = [];

    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }

    /**
     * @param  Collection<int, mixed>  $rows
     */
    public function collection(Collection $rows): void
    {
        // Use Data Map from documentation:
        // kode_bank -> get bank's id by bank's code (reference lookup)
        // nomor_rekening -> account_number (direct mapping)
        // atas_nama -> by_name (direct mapping)

        $banks = Bank::all();

        foreach ($rows as $row) {
            // Reference lookup: get bank's id by bank's code
            $bank = $banks->where('code', $row['kode_bank'])->first();

            if (! $bank) {
                $this->errors[] = "Bank dengan kode '{$row['kode_bank']}' tidak ditemukan.";

                continue;
            }

            {Resource}::create([
                'bank_id' => $bank->id,
                'account_number' => $row['nomor_rekening'],
                'by_name' => $row['atas_nama'],
            ]);
        }
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            // Use Excel column names from documentation
            'kode_bank' => 'required|string|exists:banks,code',
            'nama_bank' => 'nullable|string',
            'nomor_rekening' => 'required|numeric',
            'atas_nama' => 'required|string|max:255',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function customValidationMessages(): array
    {
        return [
            'kode_bank.required' => 'Kode Bank wajib diisi.',
            'kode_bank.exists' => 'Kode Bank tidak ditemukan.',
            'nomor_rekening.required' => 'Nomor Rekening wajib diisi.',
            'atas_nama.required' => 'Atas Nama wajib diisi.',
        ];
    }

    /**
     * @return array<string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return ! empty($this->errors);
    }
}
```

**Import Class Requirements:**
- **CRITICAL:** Must be marked as `final class`
- Add `declare(strict_types=1);` at the top
- Implement required interfaces: `SkipsEmptyRows`, `ToCollection`, `WithHeadingRow`, `WithValidation`
- Add `WithMultipleSheets` for multi-sheet imports
- Use `updateOrCreate` for upsert logic or `create` for insert-only
- Include validation rules matching the documentation
- Provide custom validation messages in user-friendly language
- Handle external references with proper error collection

### 2.2 Create Template Export Classes

**File:** `app/Exports/{Resource}TemplateExport.php` (Main Template)
**File:** `app/Exports/{Resource}ImportTemplateExport.php` (Import Sheet)

**Simple Template Pattern (Single Sheet):**

```php
<?php

declare(strict_types=1);

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

final class {Resource}TemplateExport implements FromCollection, ShouldAutoSize, WithTitle
{
    /**
     * @return Collection
     */
    public function collection()
    {
        return collect([
            ['Column Header 1', 'Column Header 2'],
            ['Example Value 1', 'Example Value 2'],
        ]);
    }

    public function title(): string
    {
        return 'Import';
    }
}
```

**Complex Template Pattern (Multiple Sheets):**

**Main Template File:**
```php
<?php

declare(strict_types=1);

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

final class {Resource}TemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new {Resource}ImportTemplateExport,
            new {RelatedResource}Export,
        ];
    }
}
```

**Import Sheet Template:**
```php
<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\{RelatedModel};
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

final class {Resource}ImportTemplateExport implements FromCollection, ShouldAutoSize, WithTitle
{
    /**
     * @return Collection
     */
    public function collection()
    {
        $related = {RelatedModel}::first();

        $headers = [
            'Column 1',
            'Column 2',
            'Column 3',
        ];

        if ($related) {
            $exampleRow = [
                $related->code,
                '=VLOOKUP(A2, RelatedSheet!A:B, 2, FALSE)',
                fake()->name(),
            ];
        } else {
            $exampleRow = [
                'EXAMPLE_CODE',
                '=VLOOKUP(A2, RelatedSheet!A:B, 2, FALSE)',
                fake()->name(),
            ];
        }

        return collect([$headers, $exampleRow]);
    }

    public function title(): string
    {
        return 'Import';
    }
}
```

**Template Export Requirements:**
- **CRITICAL:** Must be marked as `final class`
- Add `declare(strict_types=1);` at the top
- Use `ShouldAutoSize` for automatic column width
- Include `WithTitle` with 'Import' as sheet name
- Provide header row and example data row
- For complex templates: include related sheet for reference
- Use `fake()` functions for example data generation
- Use actual Excel formulas where applicable

### 2.3 Update Service Layer

**File:** `app/Services/{Resource}Service.php`

Add import and template methods to existing service class.

```php
use App\Exports\{Resource}Export;
use App\Exports\{Resource}TemplateExport;
use App\Imports\{Resource}Import;

public static function template(): BinaryFileResponse
{
    return Excel::download(new {Resource}TemplateExport, '{resource}-template.xlsx');
}

public static function import($file): void
{
    Excel::import(new {Resource}Import, $file);
}
```

**Service Method Requirements:**
- Use descriptive template filename with `{resource}-template.xlsx` format
- Template method should return `BinaryFileResponse`
- Import method should accept file path parameter
- Import method should return `void`

### 2.4 Create Import Request Validation

**File:** `app/Http/Requests/{Resource}ImportRequest.php` (Specific)
OR use existing `app/Http/Requests/ImportRequest.php` (Generic)

**Specific Request Pattern:**
```php
<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Permission;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class {Resource}ImportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can(Permission::{RESOURCE}_MANAGE);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => 'required|mimes:xlsx',
        ];
    }
}
```

**Request Validation Requirements:**
- **CRITICAL:** Must be marked as `final class`
- Add `declare(strict_types=1);` at the top
- Authorize based on `{RESOURCE}_MANAGE` permission
- Validate file is required and must be xlsx format

### 2.5 Update Controller

**File:** `app/Http/Controllers/{Resource}Controller.php`

Add template and import methods to existing controller.

```php
use App\Http\Requests\{Resource}ImportRequest;

public function template(): BinaryFileResponse
{
    return {Resource}Service::template();
}

public function import({Resource}ImportRequest $request): RedirectResponse
{
    {Resource}Service::import($request->file('file'));

    return redirect()->route('{resource}.index')
        ->withSuccess('{Resource} imported successfully');
}
```

**Controller Method Requirements:**
- Template method: return BinaryFileResponse from service
- Import method: inject specific import request class
- Import method: call service with uploaded file
- Import method: redirect with success flash message

### 2.6 Update Routes

**File:** `routes/web.php`

Add template and import routes within the resource's middleware group.

```php
Route::middleware({Resource}View::class)->group(function (): void {
    Route::get('{resource}/export', [{Resource}Controller::class, 'export'])->name('{resource}.export');
    Route::get('{resource}/template', [{Resource}Controller::class, 'template'])->name('{resource}.template');
    Route::post('{resource}/import', [{Resource}Controller::class, 'import'])->name('{resource}.import');
    Route::resource('{resource}', {Resource}Controller::class);
});
```

**Route Requirements:**
- Place routes within existing resource middleware group
- Use consistent naming: `{resource}.template`, `{resource}.import`
- Template route: GET method
- Import route: POST method (file upload)

### 2.7 Update Views

**File:** `resources/views/{resource}/index.blade.php`

Add import section to the index page accordion.

```blade
@if (auth()->user()->can(\App\Enums\Permission::{RESOURCE}_MANAGE))
<div class="title">
    <i class="dropdown icon"></i>
    Import
</div>
<div class=" content">
    <p>

        {!! form()->open()->post()->action(route('{resource}.import'))->id('import')->multipart() !!}
        {!! form()->file('file')->label('File')->required() !!}
        <x-volt-link-button class="secondary" url="{{ route('{resource}.template') }}" icon="file excel">
            Template
        </x-volt-link-button>
        <x-volt-button type="submit">
            Import
        </x-volt-button>
        {!! form()->close() !!}
    </p>
</div>
@endif
```

**View Requirements:**
- Wrap in permission check for `{RESOURCE}_MANAGE`
- Use Fomantic UI accordion structure
- Use Laravolt form builder for file input
- Add template download link button
- Add submit button for import
- Use multipart form for file upload

---

## Phase 3: Testing Phase

### 3.1 Feature Tests

**File:** `tests/Feature/{Resource}/{Resource}ControllerTest.php`

Add comprehensive feature tests for import functionality.

**Test Suite Pattern:**

```php
use App\Enums\Permission;
use App\Exports\{Resource}TemplateExport;
use App\Models\{Resource};
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Laravolt\Platform\Models\Role;
use Maatwebsite\Excel\Facades\Excel;

uses(RefreshDatabase::class);

// ... existing tests ...

test('it_can_download_{resource}_template_with_view_permission', function () {
    $response = $this->actingAs($this->normalUser)
        ->get(route('{resource}.template'));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});

test('it_cannot_download_{resource}_template_without_view_permission', function () {
    $userWithoutPermission = User::factory()->create();

    $response = $this->actingAs($userWithoutPermission)
        ->get(route('{resource}.template'));

    $response->assertStatus(401);
});

test('it_can_access_import_page_with_manage_permission', function () {
    $response = $this->actingAs($this->adminUser)
        ->get(route('{resource}.index'));

    $response->assertStatus(200);
    $response->assertSee('Import');
});

test('it_can_import_{resource}_with_valid_template_file', function () {
    Storage::fake('local');

    $templateFile = 'test_import_controller.xlsx';
    Excel::store(
        new {Resource}TemplateExport,
        $templateFile,
        'local'
    );

    $fullPath = Storage::path($templateFile);

    $file = new UploadedFile(
        $fullPath,
        'test_import.xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        null,
        true
    );

    $response = $this->actingAs($this->adminUser)
        ->post(route('{resource}.import'), [
            'file' => $file,
        ]);

    $response->assertRedirect(route('{resource}.index'));
    $response->assertSessionHasNoErrors();
});

test('it_cannot_import_{resource}_without_manage_permission', function () {
    Storage::fake('local');

    $templateFile = 'test_import_no_permission.xlsx';
    Excel::store(
        new {Resource}TemplateExport,
        $templateFile,
        'local'
    );

    $fullPath = Storage::path($templateFile);

    $file = new UploadedFile(
        $fullPath,
        'test_import.xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        null,
        true
    );

    $response = $this->actingAs($this->normalUser)
        ->post(route('{resource}.import'), [
            'file' => $file,
        ]);

    $response->assertStatus(403);
});

test('it_cannot_import_{resource}_with_invalid_file_format', function () {
    $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');

    $response = $this->actingAs($this->adminUser)
        ->post(route('{resource}.import'), [
            'file' => $file,
        ]);

    $response->assertSessionHasErrors(['file']);
});

test('it_cannot_import_{resource}_without_file', function () {
    $response = $this->actingAs($this->adminUser)
        ->post(route('{resource}.import'), []);

    $response->assertSessionHasErrors(['file']);
});
```

**Feature Test Requirements:**
- Test template download with and without permissions
- Test import page visibility with manage permission
- Test successful import with valid template file
- Test import authorization (with/without manage permission)
- Test validation for invalid file formats
- Test validation for missing file
- Use `Storage::fake('local')` for file operations
- Use `Excel::store()` to create test files
- Use `UploadedFile` for file upload simulation

### 3.2 Unit Tests - Service

**File:** `tests/Unit/Services/{Resource}ServiceTest.php`

Add unit tests for service import methods.

**Test Suite Pattern:**

```php
use App\Exports\{Resource}TemplateExport;
use App\Models\{Resource};
use App\Services\{Resource}Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

uses(RefreshDatabase::class);

// ... existing tests ...

test('it_can_download_{resource}_template', function () {
    $response = {Resource}Service::template();

    expect($response)->toBeInstanceOf(BinaryFileResponse::class);
    expect($response->getFile()->getExtension())->toBe('xlsx');
});

test('it_can_import_{resource}_from_excel_file', function () {
    Storage::fake('local');

    $initialCount = {Resource}::count();

    $templateFile = 'test_template_import.xlsx';
    Excel::store(
        new {Resource}TemplateExport,
        $templateFile,
        'local'
    );

    $fullPath = Storage::path($templateFile);
    {Resource}Service::import($fullPath);

    expect({Resource}::count())->toBeGreaterThan($initialCount);
});

test('it_can_import_{resource}_from_generated_template', function () {
    Storage::fake('local');

    {Resource}::factory()->create([
        'field' => 'TEST_VALUE',
    ]);

    $initialCount = {Resource}::count();

    $templateFile = 'test_import_generated.xlsx';
    Excel::store(
        new {Resource}TemplateExport,
        $templateFile,
        'local'
    );

    $fullPath = Storage::path($templateFile);
    {Resource}Service::import($fullPath);

    expect({Resource}::count())->toBeGreaterThanOrEqual($initialCount);
});

test('it_can_generate_template_and_import_{resource}', function () {
    {Resource}::factory()->create([
        'field' => 'TEST_VALUE',
    ]);

    $templateResponse = {Resource}Service::template();
    $templatePath = $templateResponse->getFile()->getRealPath();

    expect(file_exists($templatePath))->toBeTrue();

    {Resource}Service::import($templatePath);

    expect({Resource}::where('field', 'TEST_VALUE')->exists())->toBeTrue();
});
```

**Unit Test Requirements:**
- Test template download returns BinaryFileResponse
- Test import from stored Excel file
- Test import from generated template
- Test round-trip: generate template → import
- Verify data persistence after import
- Use `Storage::fake('local')` for file operations

---

## Code Quality Standards

### Type Safety

- **CRITICAL:** Always use `declare(strict_types=1);` at the top of EVERY PHP file
- Use explicit return type declarations for all methods
- Use proper type hints for method parameters
- **All classes must be marked as `final`**
- Use PHPDoc blocks for array shapes and complex types

### Architectural Requirements

- **All Import classes must be `final`**
- **All Export classes must be `final`**
- **All Request classes must be `final`**
- Use Laravel Excel interfaces for import/export functionality
- Follow Laravel and Laravolt coding conventions

### Error Handling

- Provide clear validation messages in user-friendly language
- Handle external reference failures gracefully
- Collect and report import errors for complex imports
- Use flash messages for user feedback

### Testing Standards

- Test both success and failure scenarios
- Test permission-based access control
- Test file validation (format, presence)
- Test data persistence after import
- Use factories for test data
- Clean up storage between tests

---

## Workflow Checklist

### Phase 1: Documentation (USER)

**CRITICAL:** This phase must be completed by the USER before AI agent begins work.

- [ ] USER creates `docs/imports/{resource}.md`
- [ ] USER documents model name
- [ ] USER documents form specification
- [ ] USER documents template structure (columns, data types)
- [ ] USER documents import validation rules
- [ ] **USER creates Data Map section (REQUIRED)**
  - [ ] Map Excel columns to model fields
  - [ ] Specify direct mappings: `excel_column : model_field`
  - [ ] Specify reference lookups: `excel_column : get related's id by related's field`
  - [ ] Omit display-only columns from Data Map
- [ ] USER includes related sheets if applicable

**AI Agent Trigger:** User requests import feature implementation and documentation already exists at `docs/imports/{resource}.md`

### Phase 2: Implementation (AI AGENT)

**AI Agent Prerequisites:**
- ✅ User has created `docs/imports/{resource}.md`
- ✅ AI agent has read and understood the documentation
- ✅ AI agent has extracted Data Map for field mappings

- [ ] AI agent reads `docs/imports/{resource}.md`
- [ ] AI agent extracts model name, columns, and validation rules
- [ ] **AI agent extracts Data Map for Excel-to-model field mappings**
- [ ] AI agent creates `app/Imports/{Resource}Import.php` using Data Map
- [ ] AI agent creates `app/Exports/{Resource}TemplateExport.php`
- [ ] AI agent creates `app/Exports/{Resource}ImportTemplateExport.php` (if complex)
- [ ] AI agent updates `app/Services/{Resource}Service.php` (add template & import methods)
- [ ] AI agent creates `app/Http/Requests/{Resource}ImportRequest.php` (or use generic)
- [ ] AI agent updates `app/Http/Controllers/{Resource}Controller.php` (add template & import methods)
- [ ] AI agent updates `routes/web.php` (add template & import routes)
- [ ] AI agent updates `resources/views/{resource}/index.blade.php` (add import section)

### Phase 3: Testing (AI AGENT)

- [ ] AI agent adds template download test (with permission)
- [ ] AI agent adds template download test (without permission)
- [ ] AI agent adds import page visibility test
- [ ] AI agent adds successful import test
- [ ] AI agent adds import authorization test
- [ ] AI agent adds invalid file format test
- [ ] AI agent adds missing file test
- [ ] AI agent adds service template download test
- [ ] AI agent adds service import test
- [ ] AI agent adds round-trip test (template → import)
- [ ] AI agent runs all tests: `php artisan test --compact`

---

## Common Patterns

### Data Map Patterns

**Direct Mapping (1:1):**
```markdown
### Data Map
- kode_bank : code
- nama_bank : name
```
Implementation: `$model->code = $row['kode_bank']`

**Reference Lookup (Many-to-One):**
```markdown
### Data Map
- kode_bank : get bank's id by bank's code
```
Implementation: `$bank = Bank::where('code', $row['kode_bank'])->first(); $model->bank_id = $bank->id`

**Display-Only (Not Stored):**
```markdown
### Data Map
- kode_bank : code
- nama_bank : name
# Note: 'nama_bank' column in Excel is display-only (VLOOKUP), not stored
```
Implementation: Skip 'nama_bank' when creating model

**Mixed Pattern:**
```markdown
### Data Map
- kode_bank : get bank's id by bank's code
- nomor_rekening : account_number
- atas_nama : by_name
# 'nama_bank' is display-only, omitted from Data Map
```

### Simple Import (No External Dependencies)

Use `updateOrCreate` for idempotent imports:
```php
{Resource}::updateOrCreate(
    ['unique_key' => $row['key']],
    [
        'field1' => $row['column1'],
        'field2' => $row['column2'],
        'unique_key' => $row['key'],
    ]
);
```

### Complex Import (With External References)

Load related models once for performance:
```php
$relatedModels = RelatedModel::all();

foreach ($rows as $row) {
    $related = $relatedModels->where('code', $row['code'])->first();

    if (! $related) {
        $this->errors[] = "Related model not found: {$row['code']}";
        continue;
    }

    {Resource}::create([...]);
}
```

### Template with Example Data

Use existing data for realistic examples:
```php
$existing = Model::first();

if ($existing) {
    $example = [$existing->field, fake()->name()];
} else {
    $example = ['EXAMPLE', fake()->name()];
}
```

### Excel Formulas in Templates

Include VLOOKUP for referenced data:
```php
'=VLOOKUP(A2, RelatedSheet!A:B, 2, FALSE)'
```

---

## Common Issues & Solutions

### Data Map Not Followed

**Problem:** Import fails or data goes to wrong fields.

**Solution:** Always follow the Data Map section from documentation:
```php
// WRONG: Using Excel column name as model field
$model->kode_bank = $row['kode_bank'];

// CORRECT: Using Data Map mapping
// Data Map: kode_bank : code
$model->code = $row['kode_bank'];
```

### Import Fails Silently

**Problem:** Import succeeds but no data created.

**Solution:** Check column names in Excel match `WithHeadingRow` format. Excel headers should use snake_case.

### Validation Not Working

**Problem:** Invalid data passes validation.

**Solution:** Ensure validation rules use Excel column names (not database field names):
```php
// Use Excel column name (from documentation)
'kode_bank' => 'required'

// NOT database field name (from Data Map)
'code' => 'required' // WRONG
```

### Permission Denied on Import

**Problem:** 403 error when importing.

**Solution:** Verify user has `{RESOURCE}_MANAGE` permission, not just `{RESOURCE}_VIEW`.

### Template Download Shows Raw Data

**Problem:** Template downloads as CSV or shows raw text.

**Solution:** Ensure route returns BinaryFileResponse and controller method exists:
```php
public function template(): BinaryFileResponse
{
    return {Resource}Service::template();
}
```

---

## Quick Reference

### Import Interfaces

- `SkipsEmptyRows` - Ignore empty rows in Excel
- `ToCollection` - Process data as Laravel Collection
- `WithHeadingRow` - Use first row as column names
- `WithValidation` - Enable validation during import
- `WithMultipleSheets` - Handle multi-sheet Excel files

### Export Interfaces

- `FromCollection` - Export from Laravel Collection
- `ShouldAutoSize` - Auto-adjust column widths
- `WithTitle` - Set sheet title
- `WithMultipleSheets` - Create multi-sheet Excel files

### Service Facade

```php
use Maatwebsite\Excel\Facades\Excel;

// Store for testing
Excel::store(new Export, 'filename.xlsx', 'local');

// Download for export
Excel::download(new Export, 'filename.xlsx');

// Import
Excel::import(new Import, 'filepath.xlsx');
```

This workflow ensures consistent, maintainable, and fully tested import implementations that follow Laravel and Laravolt best practices.
