<laravel-boost-guidelines>
=== .ai/laravolt rules ===

# Laravolt-Specific AI Coding Agent Guidelines

**Role:** You are an expert developer specializing strictly in the Laravolt platform ecosystem. Your primary goal is to maximize the use of Laravolt's built-in packages, components, and workflows.

**Strict Constraint:** Do not generate standard HTML, generic Laravel Blade, or raw UI elements if a Laravolt-specific alternative exists.

### 1. Code Generation (Artisan Commands)

* **Always prioritize scaffolding files using Laravolt's built-in Artisan commands** rather than writing classes from scratch.
* **Views:** Use `php artisan make:view {name}` to generate skeleton Blade files with `<x-volt-app>` layout.
* **Tables:** Use `php artisan make:table {Name}Table` to generate Livewire-based TableView classes.
* **Charts:** Use `php artisan make:chart {Name}` to generate Livewire ApexChart classes.
* **Statistics:** Use `php artisan make:statistic {Name}` to generate Livewire statistic widget classes.
* **Listeners:** Use `php artisan make:listener {Name}` for workflow events.
* **Scaffolding CRUD:** Use `php artisan laravolt:clap` (Thunderclap) to generate complete CRUD modules from database schemas.

### 2. UI & Blade Components (Fomantic UI Based)

* **Blade Components:** Always use Laravolt's `volt-` prefixed components for UI construction. 
* **Action Buttons:** Place page-level actions inside the `actions` slot of the `<x-volt-app>` component.
* **Flash Messages:** Rely on Laravolt's `DetectFlashMessage` middleware. Use standard Laravel session flashes (`info`, `success`, `warning`, `error`), and Laravolt will automatically render them as toast notifications.

#### Blade Component Examples:

* **App Layout:** `<x-volt-app title="Dashboard"> ... </x-volt-app>`
* **Button:** `<x-volt-button icon="plus" class="pink" color="primary" size="large">Export</x-volt-button>`
* **Submit Button:** `<x-volt-submit-button icon="save" label="Save Post" />`
* **Link Button:** `<x-volt-link-button url="{{ route('posts.create') }}" icon="plus" label="New Post" class="blue" />`
* **Dropdown Button:** `<x-volt-dropdown-button label="Actions" icon="settings"> <a href="..." class="item">Action 1</a> </x-volt-dropdown-button>`
* **Card:** `<x-volt-card title="Card Title" cover="url.jpg" content="Main content" meta.before="Date" />`
* **Brand Image:** `<x-volt-brand-image src="/logo.png" alt="Brand logo" />`
* **Breadcrumb:** `<x-volt-breadcrumb><x-volt-breadcrumb-item label="Home" link="/" /><x-volt-breadcrumb-item label="Current" /></x-volt-breadcrumb>`
* **Icon:** `<x-volt-icon name="user" class="large" color="red" />`
* **Link:** `<x-volt-link href="/dashboard" icon="home">Go to Dashboard</x-volt-link>`
* **Form Wrapper:** `<x-volt-form action="/users" method="POST"> ... </x-volt-form>`
* **Media Library:** `<x-volt-media-library name="attachments" :collection="$mediaLibrary" multiple accept="image/*" />`
* **Panel:** `<x-volt-panel title="Settings" collapsed="false"> Content </x-volt-panel>`
* **Tab:** `<x-volt-tab><x-volt-tab-panel title="Tab 1" active> Content </x-volt-tab-panel></x-volt-tab>`

### 3. Form Builder

* **Never write raw HTML `<form>` or input tags.**
* Always use the Laravolt Form helper (`form()->...`) to automatically handle Fomantic UI styling, old input, and error states.
* **Layout:** You can chain `->horizontal()` to the `open()` method for a two-column side-by-side layout.

#### Form Component Examples:

* **Open/Close:** `{!! form()->open()->route('post.store') !!}` / `{!! form()->close() !!}`
* **Text / Hidden / Password / Email:** `{!! form()->text('name', $value)->label('Username') !!}`
  `{!! form()->hidden('id', $value) !!}`
  `{!! form()->password('password')->label('Password') !!}`
  `{!! form()->email('email')->label('Email') !!}`
* **Textarea / Redactor (WYSIWYG):**
  `{!! form()->textarea('note')->label('Note') !!}`
  `{!! form()->redactor('content', $value) !!}`
* **Numbers / Currency:** `{!! form()->number('age', 17)->min(7)->max(35)->step(1) !!}`
  `{!! form()->rupiah('price', $defaultValue) !!}`
* **Checkbox / Radio:**
  `{!! form()->checkbox('remember', 1, true)->label('Remember Me') !!}`
  `{!! form()->radio('status', 'active', true)->label('Active') !!}`
  `{!! form()->checkboxGroup('fruit', ['apple' => 'Apple', 'banana' => 'Banana'], 'banana') !!}`
  `{!! form()->radioGroup('fruit', ['apple' => 'Apple', 'banana' => 'Banana'], 'apple') !!}`
* **Select / Dropdown:**
  `{!! form()->select('country', ['id' => 'Indonesia', 'ms' => 'Malaysia'])->label('Country')->placeholder('--Select--') !!}`
  `{!! form()->select('tags', $options)->multiple() !!}`
  `{!! form()->selectRange('children', 1, 5) !!}`
  `{!! form()->selectMonth('month') !!}`
  `{!! form()->dropdownDB('province', 'SELECT id, name from provinces', 'id', 'name')->dependency('country') !!}`
* **Date & Time:**
  `{!! form()->date('birthday')->label('Birthday') !!}`
  `{!! form()->time('start_time')->label('Start Time') !!}`
  `{!! form()->datepicker('date', $value, $format) !!}`
  `{!! form()->timepicker('time', $value) !!}`
  `{!! form()->selectDate('myDate', 2000, 2020) !!}`
* **Files / Media / Maps:**
  `{!! form()->file('avatar') !!}`
  `{!! form()->uploader('files')->limit(1)->extensions(['jpg', 'png'])->fileMaxSize(5) !!}`
  `{!! form()->coordinate('location') !!}`
* **Grouping Actions:**
  `{!! form()->action(form()->submit('Save'), form()->button('Cancel')) !!}`

### 4. Data Tables (Livewire & Suitable)

* **Creation:** Tables must be built extending the `TableView` class (located in `app\Http\Livewire\Table`).
* **Rendering:** Render tables via Livewire tags (e.g., `<livewire:table.user-table />`).
* **Data Source:** Define the query inside the `data()` method (Array, Collection, Eloquent, Query Builder). Apply `->autoSort($this->sortPayload())` for sorting.
* **Columns:** Strictly use Laravolt's `Suitable` column classes inside the `columns(): array` method.

#### Suitable Column Examples (`Laravolt\Suitable\Columns\*`):

* **Avatar:** `Avatar::make('email', 'Avatar')`
* **Boolean:** `Boolean::make('is_active')`
* **Button:** `Button::make('permalink', 'Info')->label('Profile')->icon('external link')`
  * *Dynamic closure:* `Button::make(fn ($user) => route('users.show', $user['id']))`
* **Checkall:** `Checkall::make('id')`
* **Date & Time:** `Date::make('created_at')`, `DateTime::make('created_at')`
* **HTML & Raw:** * `Html::make('bio')` *(For executing WYSIWYG tags safely)*
  * `Raw::make(fn ($user) => $user->roles->implode('name', ', '), 'Roles')` *(Custom logic)*
* **ID & Row Number:** `Id::make()`, `RowNumber::make()`
* **Image:** `Image::make('profile_picture')->height(50)->width(50)->alt('Pic')`
* **Label:** `Label::make('status')->addClass('green')->map(['active' => 'green', 'banned' => 'red'])`
* **Number & Text:** `Number::make('salary')` *(Formats with thousands separator)*, `Text::make('name', 'Full Name')->sortable()`
* **Restful Buttons (CRUD):** `RestfulButton::make('users')->only('show', 'edit')->except('destroy')`
* **URL & View:** `Url::make('website')`, `View::make('profile')`

### 5. Laravolt ACL (Access Control List)

* **Fixed Permissions, Dynamic Roles:** Never check for roles directly in the code. Always authorize against specific permissions.
* **Permission Registry:** All permissions must be defined as constants inside the `app\Enums\Permission.php` Enum class (e.g., `const POST_DELETE = 'post.delete';`).
* **Usage:** Use the Enum constant for authorization checks (e.g., `$user->can(\App\Enums\Permission::POST_DELETE)`).

### 6. Menu Configuration

* **Configuration:** Define sidebar menus strictly within `config/laravolt/menu/*.php` using Laravolt's array schema.
* **Authorization:** Secure menu items by attaching the Enum permissions directly to the menu array (`'permissions' => [\App\Enums\Permission::DASHBOARD_VIEW]`).

### 7. Auto CRUD & Thunderclap Generators

* **Auto CRUD (Codeless):** For simple CRUDs, define the schema inside `config/laravolt/auto-crud-resources/*.php` utilizing `\Laravolt\Fields\Field::*` constants for input types.
* **Thunderclap (Scaffolding):** For complex CRUDs requiring customization, use the `php artisan laravolt:clap` command to generate modules based on database tables and custom `.stub` templates.

### 8. Statistics & Charts

* **Statistics:** Create single-value metric widgets by extending the `Laravolt\Ui\Statistic` class.
* **Charts:** Create ApexCharts visualizations (without JS) by extending `Laravolt\Charts\*` classes (e.g., `Bar`, `Line`, `Donut`) via Livewire components.

=== .ai/STANDARD-WORKFLOW rules ===

# Standard CRUD Workflow Guidelines

Based on the established patterns in the contact management implementation, these guidelines define the standard workflow for creating complete CRUD features in the Laravolt ecosystem.

## Workflow Overview

The standard workflow follows a **three-commit approach**:
1. **Feature Implementation Commit** - Complete CRUD functionality with all necessary components
2. **Testing Commit** - Comprehensive test coverage for the implemented feature

## Phase 1: Feature Implementation (Commit 1)

### 1.1 Permissions & Authorization

**File: `app/Enums/Permission.php`**

Add permission constants to the Permission enum:
```php
// {FEATURE}
const {FEATURE}_VIEW = '{feature}:view';
const {FEATURE}_MANAGE = '{feature}:manage';
```

**Pattern:** Use `{FEATURE}:view` for read access and `{FEATURE}:manage` for create/update/delete access.

### 1.2 Database Layer

**Migration:** Create table with proper schema
- Use `php artisan make:migration` command
- Include `id()`, `timestamps()`, and `softDeletes()`
- Add foreign key constraints with proper `onDelete` behavior
- Index frequently queried columns

**Model:** Create Eloquent model
- **CRITICAL:** Must be marked as `final class`
- Add `declare(strict_types=1);` at the top
- Add `AutoFilter`, `AutoSearch`, `AutoSort` traits from Laravolt Suitable
- Define `$fillable` properties for mass assignment
- Add relationships to other models if needed (e.g., `belongsTo(User::class)`)

**Factory:** Create database factory
- **CRITICAL:** Must be marked as `final class`
- Add `declare(strict_types=1);` at the top
- Use `php artisan make:factory` command
- Define realistic dummy data using Faker
- Associate with parent models where appropriate

### 1.3 Service Layer

**File: `app/Services/{Feature}Service.php`**

Create static methods for all business logic:
```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\{Feature};
use Illuminate\Http\Request;

final class {Feature}Service
{
    public static function save(Request $request)
    public static function get($id) 
    public static function update(Request $request, $id)
    public static function delete($id)
}
```

**Pattern:** Service layer handles all data operations and user-scoped queries. Controllers remain thin.

### 1.4 Request Validation

**File: `app/Http/Requests/{Feature}Request.php`**

Create Form Request class with:
```php
<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class {Feature}Request extends FormRequest
{
    // validation rules and authorization
}
```

**Requirements:**
- **CRITICAL:** Must be marked as `final class`
- Add `declare(strict_types=1);` at the top
- Proper authorization logic
- Comprehensive validation rules
- Custom error messages if needed

### 1.5 Controller Layer

**File: `app/Http/Controllers/{Feature}Controller.php`**

Create resource controller with standard methods:
```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\{Feature}Request;
use App\Models\{Feature};
use App\Services\{Feature}Service;

final class {Feature}Controller
{
    public function index()
    public function create()
    public function store({Feature}Request $request)
    public function show(string $id)
    public function edit(string $id)
    public function update({Feature}Request $request, string $id)
    public function destroy({Feature} ${feature})
}
```

**CRITICAL Requirements:**
- **Must be marked as `final class`**
- **DO NOT extend any Controller class** - standalone class
- Add `declare(strict_types=1);` at the top
- Inject service layer and return proper responses with flash messages

### 1.6 Authorization Middleware

**File: `app/Http/Middleware/{Feature}View.php`**

Create middleware for permission-based access control:
```php
<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\Permission;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class {Feature}View
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->can(Permission::{FEATURE}_VIEW)) {
            return $next($request);
        }

        return abort(401);
    }
}
```

**CRITICAL:** Must be marked as `final class` with `declare(strict_types=1);`

### 1.7 Livewire Data Table

**File: `app/Http/Livewire/Table/{Feature}Table.php`**

Create TableView class extending `Laravolt\Ui\TableView`:
```php
<?php

declare(strict_types=1);

namespace App\Http\Livewire\Table;

use App\Enums\Permission;
use App\Models\{Feature};
use Laravolt\Suitable\Columns\{...};
use Laravolt\Ui\TableView;

final class {Feature}Table extends TableView
{
    public function data() // Define query with user scoping and search/sort
    public function columns(): array // Define table columns using Suitable columns
}
```

**CRITICAL:** Must be marked as `final class` with `declare(strict_types=1);`

**Column Types:** Use appropriate Suitable column types:
- **Avatar:** `Avatar::make('email', 'Avatar')`
- **Boolean:** `Boolean::make('is_active')`
- **Button:** `Button::make('permalink', 'Info')->label('Profile')->icon('external link')`
- * *Dynamic closure:* `Button::make(fn ($user) => route('users.show', $user['id']))`
- **Checkall:** `Checkall::make('id')`
- **Date & Time:** `Date::make('created_at')`, `DateTime::make('created_at')`
- **HTML & Raw:** * `Html::make('bio')` *(For executing WYSIWYG tags safely)*
- * `Raw::make(fn ($user) => $user->roles->implode('name', ', '), 'Roles')` *(Custom logic)*
- **ID & Row Number:** `Id::make()`, `RowNumber::make()`
- **Image:** `Image::make('profile_picture')->height(50)->width(50)->alt('Pic')`
- **Label:** `Label::make('status')->addClass('green')->map(['active' => 'green', 'banned' => 'red'])`
- **Number & Text:** `Number::make('salary')` *(Formats with thousands separator)*, `Text::make('name', 'Full Name')->sortable()`
- **Restful Buttons (CRUD):** `RestfulButton::make('users')->only('show', 'edit')->except('destroy')`
- **URL & View:** `Url::make('website')`, `View::make('profile')`

### 1.8 Views

**Directory:** `resources/views/{feature}/`

Create standard views:
- `index.blade.php` - Main listing with table
- `create.blade.php` - Creation form
- `edit.blade.php` - Edit form
- `show.blade.php` - Detail view
- `_form.blade.php` - Reusable form partial

**View Patterns:**
- Use `<x-volt-app>` wrapper with proper title
- Use `<x-volt-panel>` for content grouping
- Use `actions` slot for page-level buttons
- Use Laravolt form builder: `{!! form()->text('field')->label('Label') !!}`
- Use `<x-volt-link-button>` and `<x-volt-button>` for actions

### 1.9 Routes

**File: `routes/web.php`**

Add resource route with middleware:
```php
Route::resource('{feature}', {Feature}Controller::class)
    ->middleware({Feature}View::class);
```

### 1.10 Menu Configuration

**File: `config/laravolt/menu/menu.php`**

Add menu item with proper permissions:
```php
'{Feature}' => [
    'route' => '{feature}.index',
    'active' => '{feature}/*',
    'icon' => 'appropriate-icon',
    'permissions' => [Permission::{FEATURE}_VIEW],
],
```

## Phase 2: Testing Implementation (Commit 2)

### 2.1 Test Environment Setup

**File: `.env.testing`**
- Create testing-specific environment configuration
- Use separate test database
- Configure appropriate cache and session drivers

### 2.2 Test Fixtures

**Directory: `tests/fixtures/icons/duotone/`**

Create minimal SVG stubs for icons used in tests:
```svg
<svg></svg>
```

**Pattern:** Create stubs for all icons referenced in views and components.

### 2.3 Feature Tests

**File: `tests/Feature/{Feature}/{Feature}ControllerTest.php`**

Create comprehensive feature tests covering:

**Permission Testing:**
- Access with proper permissions should succeed
- Access without permissions should fail (401)
- Role-based permission checking

**CRUD Operations:**
- `test_it_can_access_{feature}_index_with_view_permission`
- `test_it_cannot_access_{feature}_index_without_view_permission`
- `test_it_can_access_{feature}_create_with_manage_permission`
- `test_it_can_store_a_{feature}`
- `test_it_can_display_{feature}_detail`
- `test_it_can_display_{feature}_edit_page`
- `test_it_can_update_a_{feature}`
- `test_it_can_delete_a_{feature}`

**Test Setup Pattern:**
```php
beforeEach(function (): void {
    $this->user = User::factory()->create();
    
    // Create permissions in DB
    $this->viewPermission = LaravoltPermission::firstOrCreate(['name' => Permission::{FEATURE}_VIEW]);
    $this->managePermission = LaravoltPermission::firstOrCreate(['name' => Permission::{FEATURE}_MANAGE]);
    
    // Create roles and assign permissions
    // Define Gates manually
    $this->withoutMiddleware(ValidateCsrfToken::class);
});
```

### 2.4 Unit Tests - Model

**File: `tests/Unit/Models/{Feature}Test.php`**

Test model characteristics:
- Relationship definitions
- Fillable attributes
- Soft delete functionality
- Custom methods or scopes

### 2.5 Unit Tests - Service

**File: `tests/Unit/Services/{Feature}ServiceTest.php`**

Test all service methods:
- `test_it_can_save_a_{feature}`
- `test_it_can_get_a_{feature}_by_id`
- `test_it_cannot_get_{feature}_belonging_to_another_user`
- `test_it_can_update_a_{feature}`
- `test_it_can_delete_a_{feature}`

**Pattern:** Test business logic, user scoping, and edge cases.

## Testing Standards

### Test Execution

- Use Pest testing framework syntax
- Run tests with `php artisan test --compact`
- Use descriptive test names that explain what is being tested

### Test Structure

- Use `beforeEach()` for common setup
- Use factories for test data
- Clean up database between tests
- Test both success and failure scenarios

### Coverage Goals

- Test all controller methods
- Test all service methods  
- Test permission checks
- Test user data scoping
- Test database operations

## Code Quality Standards

### Type Safety

- **CRITICAL:** Always use `declare(strict_types=1);` at the top of EVERY PHP file
- Use explicit return type declarations for all methods and functions
- Use proper type hints for method parameters
- **All classes must be marked as `final` unless they are explicitly designed for extension**
- Avoid inheritance - prefer composition over inheritance
- Do not extend the base `Controller` class - create standalone controller classes

### Architectural Requirements (ArchTest Compliance)

- **All classes must be `final`** - Models, Services, Controllers, Requests, Middleware, Factories, Seeders, Livewire components
- **All files must have `declare(strict_types=1);`** - No exceptions, including tests, factories, seeders
- **No inheritance of local `Controller` class** - Create standalone controller classes
- **No abstract classes in custom code** - Use interfaces or concrete implementations
- **All form requests must be final** - Mark request classes as `final class`
- **All service classes must be final** - Prevent extension of business logic classes
- **All models must be final** - Unless explicitly designed for inheritance
- **All factories must be final** - Prevent extension of factory classes
- **All seeders must be final** - Prevent extension of seeder classes
- **All Livewire components must be final** - Prevent extension of UI components

### Laravolt Components

- Use `<x-volt-app>` for page layouts
- Use Laravolt form builder for all forms
- Use Suitable columns for tables
- Use Laravolt buttons and links
- Follow Fomantic UI conventions

### Error Handling

- Use flash messages for user feedback
- Proper HTTP status codes
- Graceful error handling in services

### Database Best Practices

- Use Eloquent relationships
- Prevent N+1 queries with eager loading
- Use proper foreign key constraints
- Index frequently queried columns
- Use soft deletes for data retention

## Workflow Checklist

### Phase 1: Feature Implementation

- [ ] Add permissions to `app/Enums/Permission.php`
- [ ] Create database migration with proper schema
- [ ] Create Eloquent model with traits and relationships
- [ ] Create database factory with realistic data
- [ ] Create service class with static methods
- [ ] Create Form Request validation class
- [ ] Create resource controller
- [ ] Create authorization middleware
- [ ] Create Livewire TableView class
- [ ] Create all Blade views with Laravolt components
- [ ] Add routes with middleware
- [ ] Add menu configuration
- [ ] Run migration and test manually

### Phase 2: Testing Implementation

- [ ] Create `.env.testing` configuration
- [ ] Create icon fixture stubs
- [ ] Create feature test with permission tests
- [ ] Create feature test with CRUD operation tests
- [ ] Create unit test for model
- [ ] Create unit test for service
- [ ] Run all tests and ensure coverage
- [ ] Add coverage to `.gitignore` if needed

## Common Patterns

### User-Scoped Queries

```php
$me = auth()->user();
$query = Model::query();
```

### Flash Messages

```php
return redirect()->route('{feature}.index')
    ->withSuccess('Success message');
return redirect()->back()->with('error', 'Error message');
```

### Form Integration

```php
{!! form()->open()->post()->action(route('{feature}.store')) !!}
{!! form()->bind($model)->put()->action(route('{feature}.update', $model->id)) !!}
```

### Permission-Based UI

```php
@if (auth()->user()->can(\App\Enums\Permission::{FEATURE}_MANAGE))
    <!-- Manage UI elements -->
@endif
```

This workflow ensures consistent, maintainable, and fully tested CRUD implementations that follow Laravel and Laravolt best practices.

## Common Issues & Fixes

### ArchTest Failures - "Expecting X to be final"

**Problem:** The architectural testing preset requires all classes to be marked as `final` and all files to use strict types.

**Common Files That Need Fixing:**
```php
// Models (Contact.php, Product.php)
final class Contact extends Model  // ❌ class Contact extends Model

// Services (ContactService.php, ProductService.php)
final class ContactService  // ❌ class ContactService

// Controllers (ContactController.php, ProductController.php)
final class ContactController  // ❌ class ContactController extends Controller

// Requests (ContactRequest.php, ProductRequest.php)
final class ContactRequest extends FormRequest  // ❌ class ContactRequest

// Middleware (ContactView.php, ProductView.php)
final class ContactView  // ❌ class ContactView

// Livewire Components (ContactTable.php, ProductTable.php)
final class ContactTable extends TableView  // ❌ class ContactTable

// Factories (ContactFactory.php, ProductFactory.php, UserFactory.php)
final class ContactFactory extends Factory  // ❌ class ContactFactory

// Seeders (DatabaseSeeder.php)
final class DatabaseSeeder extends Seeder  // ❌ class DatabaseSeeder
```

### ArchTest Failures - "Expecting X to use strict types"

**Problem:** Missing `declare(strict_types=1);` at the top of files.

**Fix:** Add to the top of EVERY PHP file:
```php
<?php

declare(strict_types=1);  // ADD THIS LINE

namespace App\...;
```

### ArchTest Failures - "Expecting App\Http\Controllers\Controller not to be used"

**Problem:** Controllers extending the local `Controller` class.

**Fix:** Remove `extends Controller` from controllers:
```php
// ❌ WRONG
class ContactController extends Controller
{
}

// ✅ CORRECT
final class ContactController
{
}
```

**Note:** If the base `Controller.php` file exists and is unused, delete it:
```bash
rm app/Http/Controllers/Controller.php
```

### Quick Fix Checklist

When `php artisan test --compact` fails with ArchTest errors:

1. **Add strict types** to all files:
   ```bash
   # Find files missing strict types

   grep -L "declare(strict_types=1)" app/Models/*.php
   grep -L "declare(strict_types=1)" app/Services/*.php
   grep -L "declare(strict_types=1)" app/Http/Controllers/*.php
   grep -L "declare(strict_types=1)" app/Http/Requests/*.php
   grep -L "declare(strict_types=1)" app/Http/Middleware/*.php
   grep -L "declare(strict_types=1)" app/Http/Livewire/Table/*.php
   grep -L "declare(strict_types=1)" database/factories/*.php
   grep -L "declare(strict_types=1)" database/seeders/*.php
   ```

2. **Make classes final:**
   - Models: `final class ModelName extends Model`
   - Services: `final class ServiceName`
   - Controllers: `final class ControllerName` (no extends)
   - Requests: `final class RequestName extends FormRequest`
   - Middleware: `final class MiddlewareName`
   - Livewire: `final class ComponentName extends TableView`
   - Factories: `final class FactoryName extends Factory`
   - Seeders: `final class SeederName extends Seeder`

3. **Run code formatting:**
   ```bash
   vendor/bin/pint --dirty --format agent
   ```

4. **Verify all tests pass:**
   ```bash
   php artisan test --compact
   # Should show: "Tests: 86+ passed"

   ```

### Prevention Tips

**When creating new classes, always use this template:**
```php
<?php

declare(strict_types=1);

namespace App\{Namespace};

use {RequiredImports};

final class {ClassName}
{
    // class implementation
}
```

**When extending framework classes:**
- Controllers: **DO NOT** extend local Controller, create standalone
- Models: `final class ModelName extends Model`
- Requests: `final class RequestName extends FormRequest`
- Factories: `final class FactoryName extends Factory`
- Livewire: `final class ComponentName extends TableView/Component`

This proactive approach prevents ArchTest failures and ensures clean, maintainable code architecture.

=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.3.30
- larastan/larastan (LARASTAN) - v3
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- livewire/livewire (LIVEWIRE) - v3
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- tailwindcss (TAILWINDCSS) - v4

## Skills Activation

This project has domain-specific skills available. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

- `pest-testing` — Tests applications using the Pest 4 PHP framework. Activates when writing tests, creating unit or feature tests, adding assertions, testing Livewire components, browser testing, debugging test failures, working with datasets or mocking; or when the user mentions test, spec, TDD, expects, assertion, coverage, or needs to verify functionality works.
- `tailwindcss-development` — Styles applications using Tailwind CSS v4 utilities. Activates when adding styles, restyling components, working with gradients, spacing, layout, flex, grid, responsive design, dark mode, colors, typography, or borders; or when the user mentions CSS, styling, classes, Tailwind, restyle, hero section, cards, buttons, or any visual/UI changes.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan Commands

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`, `php artisan tinker --execute &quot;...&quot;`).
- Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.

## URLs

- Whenever you share a project URL with the user, you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain/IP, and port.

## Debugging

- Use the `database-query` tool when you only need to read from the database.
- Use the `database-schema` tool to inspect table structure before writing migrations or models.
- To execute PHP code for debugging, run `php artisan tinker --execute &quot;your code here&quot;` directly.
- To read configuration values, read the config files directly or run `php artisan config:show [key]`.
- To inspect routes, run `php artisan route:list` directly.
- To check environment variables, read the `.env` file directly.

## Reading Browser Logs With the `browser-logs` Tool

- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)

- Boost comes with a powerful `search-docs` tool you should use before trying other approaches when working with Laravel or Laravel ecosystem packages. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic-based queries at once. For example: `['rate limiting', 'routing rate limiting', 'routing']`. The most relevant results will be returned first.
- Do not add package names to queries; package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'.
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit".
3. Quoted Phrases (Exact Position) - query="infinite scroll" - words must be adjacent and in that order.
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit".
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms.

=== php rules ===

# PHP

- Always use strict typing at the head of a `.php` file: `declare(strict_types=1);`.
- Always use curly braces for control structures, even for single-line bodies.

## Constructors

- Use PHP 8 constructor property promotion in `__construct()`.
    - `public function __construct(public GitHub $github) { }`
- Do not allow empty `__construct()` methods with zero parameters unless the constructor is private.

## Type Declarations

- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<!-- Explicit Return Types and Method Params -->
```php
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
```

## Enums

- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.

## Comments

- Prefer PHPDoc blocks over inline comments. Never use comments within the code itself unless the logic is exceptionally complex.

## PHPDoc Blocks

- Add useful array shape type definitions when appropriate.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

## Database

- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries.
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

### APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## Controllers & Validation

- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

## Authentication & Authorization

- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Queues

- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

## Configuration

- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v12 rules ===

# Laravel 12

- CRITICAL: ALWAYS use `search-docs` tool for version-specific Laravel documentation and updated code examples.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

## Laravel 12 Structure

- In Laravel 12, middleware are no longer registered in `app/Http/Kernel.php`.
- Middleware are configured declaratively in `bootstrap/app.php` using `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- The `app\Console\Kernel.php` file no longer exists; use `bootstrap/app.php` or `routes/console.php` for console configuration.
- Console commands in `app/Console/Commands/` are automatically available and do not require manual registration.

## Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.
- CRITICAL: ALWAYS use `search-docs` tool for version-specific Pest documentation and updated code examples.
- IMPORTANT: Activate `pest-testing` every time you're working with a Pest or testing-related task.

=== tailwindcss/core rules ===

# Tailwind CSS

- Always use existing Tailwind conventions; check project patterns before adding new ones.
- IMPORTANT: Always use `search-docs` tool for version-specific Tailwind CSS documentation and updated code examples. Never rely on training data.
- IMPORTANT: Activate `tailwindcss-development` every time you're working with a Tailwind CSS or styling-related task.

</laravel-boost-guidelines>
