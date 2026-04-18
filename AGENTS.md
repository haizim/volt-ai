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
* **Submit Button:** `<x-volt-button icon="save" label="Save Post" />`
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

=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.3.30
- larastan/larastan (LARASTAN) - v3
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- laravel/sanctum (SANCTUM) - v4
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
