# Standard CRUD Workflow Guidelines

Based on the established patterns in the contact management implementation, these guidelines define the standard workflow for creating complete CRUD features in the Laravolt ecosystem.

## Workflow Overview

The standard workflow follows a **three-commit approach**:
1. **Feature Implementation Commit** - Complete CRUD functionality with all necessary components
2. **Code Quality Commit** - Architectural compliance fixes and final polish
3. **Testing Commit** - Comprehensive test coverage for the implemented feature

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
- Add relationships to other models (e.g., `belongsTo(User::class)`)

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

### 1.11 User Model Relationships

**File: `app/Models/User.php`**

Add relationship to the new model:
```php
public function {feature}s()
{
    return $this->hasMany({Feature}::class);
}
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
- [ ] Update User model with relationships
- [ ] Run migration and test manually

### Phase 2: Code Quality & Architectural Compliance
- [ ] **CRITICAL:** Ensure ALL files have `declare(strict_types=1);`
- [ ] **CRITICAL:** Ensure ALL classes are marked as `final`
- [ ] Run `php artisan test --compact` to check for ArchTest failures
- [ ] Fix any architectural violations:
  - [ ] Models must be `final class`
  - [ ] Services must be `final class`
  - [ ] Controllers must be `final class` (not extending base Controller)
  - [ ] Requests must be `final class`
  - [ ] Middleware must be `final class`
  - [ ] Factories must be `final class`
  - [ ] Seeders must be `final class`
  - [ ] Livewire components must be `final class`
- [ ] Run `vendor/bin/pint --dirty --format agent` for code formatting

### Phase 3: Testing Implementation
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
$query = Model::query()->where('user_id', $me->id);
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