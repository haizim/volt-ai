# Standard CRUD Workflow Guidelines

Based on the established patterns in the contact management implementation, these guidelines define the standard workflow for creating complete CRUD features in the Laravolt ecosystem.

## Workflow Overview

The standard workflow follows a two-commit approach:
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
- Add `AutoFilter`, `AutoSearch`, `AutoSort` traits from Laravolt Suitable
- Define `$fillable` properties for mass assignment
- Add relationships to other models (e.g., `belongsTo(User::class)`)

**Factory:** Create database factory
- Use `php artisan make:factory` command
- Define realistic dummy data using Faker
- Associate with parent models where appropriate

### 1.3 Service Layer

**File: `app/Services/{Feature}Service.php`**

Create static methods for all business logic:
```php
public static function save(Request $request)
public static function get($id) 
public static function update(Request $request, $id)
public static function delete($id)
```

**Pattern:** Service layer handles all data operations and user-scoped queries. Controllers remain thin.

### 1.4 Request Validation

**File: `app/Http/Requests/{Feature}Request.php`**

Create Form Request class with:
- Proper authorization logic
- Comprehensive validation rules
- Custom error messages if needed

### 1.5 Controller Layer

**File: `app/Http/Controllers/{Feature}Controller.php`**

Create resource controller with standard methods:
- `index()` - Display listing with Livewire table
- `create()` - Show creation form
- `store(Request)` - Handle form submission
- `show($id)` - Display single record
- `edit($id)` - Show edit form
- `update(Request, $id)` - Handle update
- `destroy(Model)` - Handle deletion

**Pattern:** Inject service layer and return proper responses with flash messages.

### 1.6 Authorization Middleware

**File: `app/Http/Middleware/{Feature}View.php`**

Create middleware for permission-based access control:
```php
if (auth()->user()->can(Permission::{FEATURE}_VIEW)) {
    return $next($request);
}
return abort(401);
```

### 1.7 Livewire Data Table

**File: `app/Http/Livewire/Table/{Feature}Table.php`**

Create TableView class extending `Laravolt\Ui\TableView`:
```php
public function data() // Define query with user scoping and search/sort
public function columns(): array // Define table columns using Suitable columns
```

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
- Always use `declare(strict_types=1);` at file top
- Use explicit return type declarations
- Use proper type hints for parameters

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