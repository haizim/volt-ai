# API Implementation Workflow Guidelines

Based on commit `a7b77a6` - Bank API implementation, this workflow defines the standard approach for implementing RESTful APIs in the Laravolt ecosystem.

## Documentation-Driven Development

This workflow follows a **documentation-first** approach:

1. **User** creates comprehensive API documentation in `docs/api/{resource}.md`
2. **AI Agent** reads the documentation and implements the complete API
3. **AI Agent** creates comprehensive test coverage

This ensures that:
- The API specification is clearly defined before implementation begins
- The AI agent has a complete reference to work from
- Implementation matches the documented requirements exactly

## Workflow Overview

The API implementation follows a three-phase approach with clear role separation:

1. **Phase 1: Documentation (USER)** - User creates API specification document
2. **Phase 2: Implementation (AI AGENT)** - AI agent reads documentation and builds the API
3. **Phase 3: Testing (AI AGENT)** - AI agent creates comprehensive feature and unit tests

### Role Responsibilities

**User Responsibilities:**
- Create `docs/api/{resource}.md` with complete API specification
- Define route name, model, and searchable fields
- Specify validation requirements and permissions

**AI Agent Responsibilities:**
- Read and understand the API documentation
- Implement the service layer, controller, and routes based on docs
- Create comprehensive test coverage for all endpoints

## Phase 1: Documentation (User Responsibility)

### 1.1 Create API Documentation

**Action:** User creates `docs/api/{resource}.md`

Define the API specification in a markdown file that the AI agent will use as a reference:

```markdown
# API {Resource}

## Route

{resource-route}

## Model

{ModelName}

## Searchable

- {field1}
- {field2}
```

**Example:**
```markdown
# API Bank

## Route

banks

## Model

Bank

## Searchable

- name
- code
```

### 1.2 Define Requirements

**Action:** User identifies and documents requirements

The documentation should include:
- **HTTP Methods**: GET (index, show), POST (store), PUT/PATCH (update), DELETE (destroy)
- **Searchable Fields**: Which fields can be queried via URL parameters
- **Validation Rules**: Required fields, unique constraints, max lengths
- **Authorization**: Which permissions are required for each operation
- **Response Format**: Standard JSON structure

**Once the documentation is complete, the user requests the AI agent to implement the API based on the docs.**

---

## Phase 2: Implementation (AI Agent Responsibility)

### AI Agent Workflow

1. **Read Documentation** - Read `docs/api/{resource}.md` to understand requirements
2. **Read Existing Patterns** - Study similar existing API implementations (e.g., BankController)
3. **Implement** - Build the API following the documented specification
4. **Verify** - Ensure all code quality standards are met

### 2.1 Service Layer

**File:** `app/Services/{Resource}Service.php`

Add a `search()` method for listing and filtering resources:

```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\{Resource};
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

final class {Resource}Service
{
    // Existing methods...

    public static function search(Request $request): Collection
    {
        $searchable = ['field1', 'field2', 'field3'];

        $queries = array_intersect_key($request->all(), array_flip($searchable));

        $resources = {Resource}::query();

        foreach ($queries as $key => $value) {
            $resources->where($key, $value);
        }

        return $resources->get();
    }
}
```

**Pattern:**
- Define searchable fields as an array
- Use `array_intersect_key()` to filter only allowed search parameters
- Apply exact match queries (can be enhanced with LIKE for partial matching)
- Return Eloquent Collection

### 2.2 Form Request Validation

**File:** `app/Http/Requests/{Resource}Request.php`

Create validation rules with proper authorization:

```php
<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Permission;
use Illuminate\Foundation\Http\FormRequest;

final class {Resource}Request extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(Permission::{RESOURCE}_MANAGE);
    }

    public function rules(): array
    {
        $uniqueField = 'unique:{table},{field}';

        if ($this->method() === 'PUT' || $this->method() === 'PATCH') {
            $uniqueField .= ','.$this->{resource};
        }

        return [
            'field1' => ['required', 'string', 'max:255'],
            'field2' => ['required', 'string', 'max:10', $uniqueField],
        ];
    }

    public function attributes(): array
    {
        return [
            'field1' => 'Field One Label',
            'field2' => 'Field Two Label',
        ];
    }
}
```

**CRITICAL Requirements:**
- Must be marked as `final class`
- Must use `declare(strict_types=1);`
- Authorization checks permission for write operations
- Unique validation must exclude current record on updates
- Provide human-readable attribute names for error messages

### 2.3 API Controller

**File:** `app/Http/Controllers/Api/{Resource}Controller.php`

Create RESTful controller using `ApiResponse` for consistent responses:

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\{Resource}Request;
use App\Http\Responses\ApiResponse;
use App\Services\{Resource}Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class {Resource}Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $resources = {Resource}Service::search($request);

        return ApiResponse::success($resources);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store({Resource}Request $request): JsonResponse
    {
        $resource = {Resource}Service::save($request);

        return ApiResponse::success($resource);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $resource = {Resource}Service::get($id);

        return ApiResponse::success($resource);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update({Resource}Request $request, int $id): JsonResponse
    {
        $resource = {Resource}Service::update($request, $id);

        return ApiResponse::success($resource);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        {Resource}Service::delete($id);

        return ApiResponse::success();
    }
}
```

**CRITICAL Requirements:**
- Must be marked as `final class`
- Must use `declare(strict_types=1);`
- DO NOT extend any base Controller class - standalone class
- All methods return `JsonResponse`
- Use `ApiResponse::success()` for consistent JSON responses
- Controller delegates business logic to service layer

**ApiResponse Methods:**
- `ApiResponse::success($data, $message, $status)` - Successful response (default 200)
- `ApiResponse::error($message, $errors, $status)` - Error response (default 400)
- `ApiResponse::unauthorized($message)` - 401 Unauthorized
- `ApiResponse::forbidden($message)` - 403 Forbidden
- `ApiResponse::notFound($message)` - 404 Not Found
- `ApiResponse::validationError($message, $errors)` - 422 Validation Error
- `ApiResponse::created($data, $message)` - 201 Created

### 2.4 API Routes

**File:** `routes/api.php`

Register API resource routes with authentication middleware:

```php
<?php

declare(strict_types=1);

use App\Http\Controllers\Api\{Resource}Controller;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    // Existing routes...

    Route::middleware('auth:sanctum')->group(function (): void {
        // Existing authenticated routes...

        Route::apiResource('{resource-plural}', {Resource}Controller::class);
    });
});
```

**Pattern:**
- All API routes use `auth:sanctum` middleware
- Routes are namespaced with `api.` prefix
- Use `Route::apiResource()` for RESTful endpoints
- Resource routes: index, store, show, update, destroy

---

## Phase 3: Testing (AI Agent Responsibility)

### AI Agent Testing Workflow

After completing implementation, the AI agent creates comprehensive test coverage:

1. **Feature Tests** - Test all API endpoints with authentication, authorization, validation
2. **Unit Tests** - Test service layer methods including search functionality
3. **Edge Cases** - Test empty results, not found errors, duplicate values
4. **Run Tests** - Execute `php artisan test --compact` to verify all tests pass

### 3.1 Feature Tests - API Endpoints

**File:** `tests/Feature/Api/{Resource}ControllerTest.php`

Create comprehensive API endpoint tests:

```php
<?php

declare(strict_types=1);

use App\Enums\Permission;
use App\Models\{Resource};
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Laravolt\Platform\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->viewPermission = Laravolt\Platform\Models\Permission::firstOrCreate(['name' => Permission::{RESOURCE}_VIEW]);
    $this->managePermission = Laravolt\Platform\Models\Permission::firstOrCreate(['name' => Permission::{RESOURCE}_MANAGE]);

    $this->adminRole = Role::firstOrCreate(['name' => 'admin']);
    $this->userRole = Role::firstOrCreate(['name' => 'user']);

    $this->adminRole->permissions()->attach($this->viewPermission);
    $this->adminRole->permissions()->attach($this->managePermission);
    $this->userRole->permissions()->attach($this->viewPermission);

    $this->adminUser = User::factory()->create();
    $this->adminUser->roles()->attach($this->adminRole);

    $this->normalUser = User::factory()->create();
    $this->normalUser->roles()->attach($this->userRole);

    Gate::before(function (User $user, string $ability) {
        return $user->roles->flatMap->permissions->contains('name', $ability);
    });

    $this->token = $this->adminUser->createToken('test-token')->plainTextToken;
});

test('it_can_get_list_of_{resources}', function () {
    {Resource}::factory()->count(3)->create();

    $response = $this->withToken($this->token)
        ->getJson('/api/{resource-plural}');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'field1',
                    'field2',
                ],
            ],
        ])
        ->assertJsonPath('success', true)
        ->assertJsonCount(3, 'data');
});

test('it_can_search_{resources}_by_field', function () {
    {Resource}::factory()->create([
        'field1' => 'Value 1',
        'field2' => 'CODE1',
    ]);

    {Resource}::factory()->create([
        'field1' => 'Value 2',
        'field2' => 'CODE2',
    ]);

    $response = $this->withToken($this->token)
        ->getJson('/api/{resource-plural}?field1=Value 1');

    $response->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.field1', 'Value 1')
        ->assertJsonPath('data.0.field2', 'CODE1');
});

test('it_can_show_single_{resource}', function () {
    $resource = {Resource}::factory()->create([
        'field1' => 'Test Value',
    ]);

    $response = $this->withToken($this->token)
        ->getJson("/api/{resource-plural}/{$resource->id}");

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'field1',
                'field2',
            ],
        ])
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.id', $resource->id)
        ->assertJsonPath('data.field1', 'Test Value');
});

test('it_returns_not_found_when_showing_non_existent_{resource}', function () {
    $response = $this->withToken($this->token)
        ->getJson('/api/{resource-plural}/999');

    $response->assertNotFound();
});

test('it_can_create_a_{resource}', function () {
    $data = [
        'field1' => 'New Value',
        'field2' => 'NEW',
    ];

    $response = $this->withToken($this->token)
        ->postJson('/api/{resource-plural}', $data);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'field1',
                'field2',
            ],
        ])
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.field1', 'New Value')
        ->assertJsonPath('data.field2', 'NEW');

    $this->assertDatabaseHas('{table-name}', $data);
});

test('it_fails_validation_when_creating_{resource}_without_required_field', function () {
    $response = $this->withToken($this->token)
        ->postJson('/api/{resource-plural}', [
            'field2' => 'VALUE',
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['field1']);
});

test('it_fails_validation_when_creating_{resource}_with_duplicate_unique_field', function () {
    {Resource}::factory()->create([
        'field2' => 'UNIQUE',
    ]);

    $response = $this->withToken($this->token)
        ->postJson('/api/{resource-plural}', [
            'field1' => 'Another Name',
            'field2' => 'UNIQUE',
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['field2']);
});

test('it_can_update_a_{resource}', function () {
    $resource = {Resource}::factory()->create([
        'field1' => 'Old Value',
    ]);

    $updateData = [
        'field1' => 'Updated Value',
        'field2' => 'UPD',
    ];

    $response = $this->withToken($this->token)
        ->putJson("/api/{resource-plural}/{$resource->id}", $updateData);

    $response->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.field1', 'Updated Value')
        ->assertJsonPath('data.field2', 'UPD');

    $this->assertDatabaseHas('{table-name}', array_merge(['id' => $resource->id], $updateData));
});

test('it_returns_not_found_when_updating_non_existent_{resource}', function () {
    $response = $this->withToken($this->token)
        ->putJson('/api/{resource-plural}/999', [
            'field1' => 'Updated',
        ]);

    $response->assertNotFound();
});

test('it_can_update_{resource}_with_same_unique_field', function () {
    $resource = {Resource}::factory()->create([
        'field2' => 'SAME',
    ]);

    $response = $this->withToken($this->token)
        ->putJson("/api/{resource-plural}/{$resource->id}", [
            'field1' => 'Updated Name',
            'field2' => 'SAME',
        ]);

    $response->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.field1', 'Updated Name')
        ->assertJsonPath('data.field2', 'SAME');
});

test('it_can_delete_a_{resource}', function () {
    $resource = {Resource}::factory()->create();

    $response = $this->withToken($this->token)
        ->deleteJson("/api/{resource-plural}/{$resource->id}");

    $response->assertSuccessful()
        ->assertJsonPath('success', true);

    $this->assertSoftDeleted('{table-name}', [
        'id' => $resource->id,
    ]);
});

test('it_returns_not_found_when_deleting_non_existent_{resource}', function () {
    $response = $this->withToken($this->token)
        ->deleteJson('/api/{resource-plural}/999');

    $response->assertNotFound();
});

test('it_cannot_access_api_without_authentication', function () {
    $response = $this->getJson('/api/{resource-plural}');

    $response->assertUnauthorized();
});

test('it_can_get_empty_list_of_{resources}', function () {
    $response = $this->withToken($this->token)
        ->getJson('/api/{resource-plural}');

    $response->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonCount(0, 'data');
});

test('it_returns_empty_results_when_search_with_non_matching_criteria', function () {
    {Resource}::factory()->create([
        'field1' => 'Existing Value',
    ]);

    $response = $this->withToken($this->token)
        ->getJson('/api/{resource-plural}?field1=Non Existent');

    $response->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonCount(0, 'data');
});
```

**Test Coverage Requirements:**
- **List (index)**: Get all resources, search by individual fields, search by multiple fields, empty list
- **Show**: Get single resource, not found error
- **Create**: Successful creation, validation failures (missing required, duplicate unique)
- **Update**: Successful update, not found error, update with same unique value
- **Delete**: Successful deletion, not found error
- **Authentication**: Unauthorized access without token
- **Edge Cases**: Empty results, non-matching searches

### 3.2 Unit Tests - Service Layer

**File:** `tests/Unit/Services/{Resource}ServiceTest.php`

Add tests for the `search()` method:

```php
<?php

declare(strict_types=1);

use App\Enums\Permission;
use App\Models\{Resource};
use App\Models\User;
use App\Services\{Resource}Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Laravolt\Platform\Models\Role;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $user = User::factory()->create();
    $managePermission = Laravolt\Platform\Models\Permission::firstOrCreate(['name' => Permission::{RESOURCE}_MANAGE]);
    $adminRole = Role::firstOrCreate(['name' => 'admin']);
    $adminRole->permissions()->attach($managePermission);
    $user->roles()->attach($adminRole);

    Gate::before(function (User $u, string $ability) {
        return $u->roles->flatMap->permissions->contains('name', $ability);
    });

    $this->actingAs($user);
});

test('it_can_search_{resources}_by_field1', function () {
    {Resource}::factory()->create([
        'field1' => 'Value A',
        'field2' => 'CODEA',
    ]);

    {Resource}::factory()->create([
        'field1' => 'Value B',
        'field2' => 'CODEB',
    ]);

    $request = Request::create('/{resource-plural}/search', 'GET', ['field1' => 'Value A']);

    $result = {Resource}Service::search($request);

    expect($result)->toHaveCount(1);
    expect($result->first()->field1)->toBe('Value A');
    expect($result->first()->field2)->toBe('CODEA');
});

test('it_can_search_{resources}_by_field2', function () {
    {Resource}::factory()->create([
        'field1' => 'Value A',
        'field2' => 'CODEA',
    ]);

    {Resource}::factory()->create([
        'field1' => 'Value B',
        'field2' => 'CODEB',
    ]);

    $request = Request::create('/{resource-plural}/search', 'GET', ['field2' => 'CODEB']);

    $result = {Resource}Service::search($request);

    expect($result)->toHaveCount(1);
    expect($result->first()->field1)->toBe('Value B');
    expect($result->first()->field2)->toBe('CODEB');
});

test('it_can_search_{resources}_by_multiple_fields', function () {
    {Resource}::factory()->create([
        'field1' => 'Value A',
        'field2' => 'CODEA',
    ]);

    {Resource}::factory()->create([
        'field1' => 'Value B',
        'field2' => 'CODEB',
    ]);

    $request = Request::create('/{resource-plural}/search', 'GET', [
        'field1' => 'Value A',
        'field2' => 'CODEA',
    ]);

    $result = {Resource}Service::search($request);

    expect($result)->toHaveCount(1);
    expect($result->first()->field1)->toBe('Value A');
    expect($result->first()->field2)->toBe('CODEA');
});

test('it_returns_all_{resources}_when_no_search_criteria', function () {
    {Resource}::factory()->count(3)->create();

    $request = Request::create('/{resource-plural}/search', 'GET', []);

    $result = {Resource}Service::search($request);

    expect($result)->toHaveCount(3);
});

test('it_returns_empty_collection_when_no_match_found', function () {
    {Resource}::factory()->create([
        'field1' => 'Existing Value',
    ]);

    $request = Request::create('/{resource-plural}/search', 'GET', ['field1' => 'Non Existent']);

    $result = {Resource}Service::search($request);

    expect($result)->toHaveCount(0);
});

test('it_ignores_non_searchable_fields', function () {
    {Resource}::factory()->create([
        'field1' => 'Value A',
    ]);

    $request = Request::create('/{resource-plural}/search', 'GET', [
        'field1' => 'Value A',
        'invalid_field' => 'some_value',
    ]);

    $result = {Resource}Service::search($request);

    expect($result)->toHaveCount(1);
    expect($result->first()->field1)->toBe('Value A');
});
```

**Service Test Coverage:**
- Search by individual fields
- Search by multiple fields (AND logic)
- Return all when no criteria
- Empty results when no match
- Ignore non-searchable fields (security)

## Code Quality Standards

### Type Safety

- **CRITICAL:** Always use `declare(strict_types=1);` at the top of EVERY PHP file
- Use explicit return type declarations for all methods
- Use proper type hints for method parameters
- **All classes must be marked as `final`** unless designed for extension

### API Response Standards

**Standard Response Structure:**
```json
{
  "success": true|false,
  "message": "Human readable message",
  "data": { ... },
  "errors": { ... } // Only on validation errors
}
```

**HTTP Status Codes:**
- `200` - OK (GET single, PUT/PATCH, DELETE)
- `201` - Created (POST)
- `400` - Bad Request
- `401` - Unauthorized (no token)
- `403` - Forbidden (has token but no permission)
- `404` - Not Found
- `422` - Validation Error

### Security Best Practices

- All endpoints require `auth:sanctum` middleware
- Form requests authorize using permissions
- Search functionality filters by allowed fields only
- Unique validation excludes current record on updates
- Never expose internal IDs in error messages

### Testing Standards

- Use Pest testing framework syntax
- Run tests with `php artisan test --compact`
- Test both success and failure scenarios
- Test authentication/authorization
- Test validation rules
- Test edge cases (empty results, not found, etc.)
- Clean up database between tests using `RefreshDatabase`

## Workflow Checklist

### Phase 1: Documentation (User Responsibility)

- [ ] **USER**: Create `docs/api/{resource}.md` with route, model, and searchable fields
- [ ] **USER**: Identify required permissions
- [ ] **USER**: Define validation rules
- [ ] **USER**: Plan response structure
- [ ] **USER**: Request AI agent to implement the API

### Phase 2: Implementation (AI Agent Responsibility)

- [ ] **AI AGENT**: Read `docs/api/{resource}.md` to understand requirements
- [ ] **AI AGENT**: Add `search()` method to existing service class
- [ ] **AI AGENT**: Create or update Form Request validation class
- [ ] **AI AGENT**: Create API controller with RESTful methods
- [ ] **AI AGENT**: Add API routes with authentication middleware
- [ ] **AI AGENT**: Verify routes with `php artisan route:list --path=api`

### Phase 3: Testing (AI Agent Responsibility)

- [ ] **AI AGENT**: Create feature tests for all endpoints
- [ ] **AI AGENT**: Test list/index with search parameters
- [ ] **AI AGENT**: Test show single resource
- [ ] **AI AGENT**: Test create with valid and invalid data
- [ ] **AI AGENT**: Test update with valid and invalid data
- [ ] **AI AGENT**: Test delete
- [ ] **AI AGENT**: Test authentication requirement
- [ ] **AI AGENT**: Create unit tests for service search method
- [ ] **AI AGENT**: Run all tests: `php artisan test --compact`
- [ ] **AI AGENT**: Ensure all tests pass

## Common Patterns

### Search Functionality

```php
// Service method
public static function search(Request $request): Collection
{
    $searchable = ['field1', 'field2'];

    $queries = array_intersect_key($request->all(), array_flip($searchable));

    $query = Model::query();

    foreach ($queries as $key => $value) {
        $query->where($key, $value);
    }

    return $query->get();
}
```

### Unique Validation with Update Support

```php
public function rules(): array
{
    $uniqueRule = 'unique:table,column';

    if ($this->method() === 'PUT' || $this->method() === 'PATCH') {
        $uniqueRule .= ','.$this->route('id'); // or $this->{resource}
    }

    return [
        'field' => ['required', $uniqueRule],
    ];
}
```

### Consistent API Responses

```php
// Success
return ApiResponse::success($data, 'Success message', 200);

// Created
return ApiResponse::created($data, 'Resource created');

// Error
return ApiResponse::error('Error message', $errors, 400);

// Not Found
return ApiResponse::notFound('Resource not found');
```

### Test Authentication Setup

```php
beforeEach(function (): void {
    // Setup permissions and roles
    // Create users with different permission levels
    // Define gate for permission checking
    // Create API token for authenticated requests

    $this->token = $user->createToken('test-token')->plainTextToken;
});

// Use in tests
$response = $this->withToken($this->token)
    ->getJson('/api/resource');
```

## Quick Reference

### File Locations

- **API Docs:** `docs/api/{resource}.md`
- **Service:** `app/Services/{Resource}Service.php`
- **Form Request:** `app/Http/Requests/{Resource}Request.php`
- **API Controller:** `app/Http/Controllers/Api/{Resource}Controller.php`
- **API Routes:** `routes/api.php`
- **Feature Tests:** `tests/Feature/Api/{Resource}ControllerTest.php`
- **Unit Tests:** `tests/Unit/Services/{Resource}ServiceTest.php`

### Artisan Commands

```bash
# List API routes
php artisan route:list --path=api

# Run tests
php artisan test --compact

# Run specific test file
php artisan test --filter {Resource}ControllerTest

# Format code
vendor/bin/pint --dirty --format agent
```

### Common HTTP Methods

- `GET /api/{resource}` - List all (with optional search params)
- `GET /api/{resource}/{id}` - Get single
- `POST /api/{resource}` - Create new
- `PUT/PATCH /api/{resource}/{id}` - Update
- `DELETE /api/{resource}/{id}` - Delete

### Search Query Parameters

```
GET /api/{resource}?field1=value1&field2=value2
```

Only fields defined in the service's `$searchable` array will be processed.

This workflow ensures consistent, secure, and well-tested API implementations following Laravel and Laravolt best practices.
