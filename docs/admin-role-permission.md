# Admin Role & Permission Module

Complete guide for the Spatie Permission + Filament admin authorization system in **ApplyDocs ZA**.

> **Note:** This project uses **Filament v3.3** (not v4). All admin UI follows Filament v3 conventions.

---

## Installation

Spatie Permission is already installed and configured.

### Package

```bash
composer require spatie/laravel-permission
```

### Publish (already done)

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

### User model

`App\Models\User` uses:

```php
use Spatie\Permission\Traits\HasRoles;
```

Supports:

- Multiple roles per user (`syncRoles`, `assignRole`)
- Direct permissions (`givePermissionTo`) — prefer roles in admin UI
- Role inheritance via Spatie

### Seed default data

```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

Or run full seed:

```bash
php artisan migrate --seed
```

---

## Architecture Flow

```
Admin login (/admin/login)
    → Filament session auth
    → User::canAccessPanel() (must have staff role)
    → Gate::before → Super Admin bypasses all checks
    → Policy viewAny / create / update / delete per resource
    → Filament hides unauthorized navigation items
    → Unauthorized direct URL → 403
```

### Assign role flow

```
Super Admin → Admin → Roles → Create/Edit role
    → Select permissions (grouped checkboxes by module)
    → Save → role.syncPermissions()

Super Admin → Admin → Users → Edit user
    → Multi-select roles
    → Save → user.syncRoles()
    → Permission summary shows effective permissions
```

---

## Permissions Map

### All permissions

| Permission | Label | Module |
|------------|-------|--------|
| `dashboard.view` | View Dashboard | Dashboard |
| `users.view` | View Users | Users |
| `users.create` | Create User | Users |
| `users.edit` | Edit User | Users |
| `users.delete` | Delete User | Users |
| `providers.view` | View Providers | Providers |
| `providers.create` | Create Provider | Providers |
| `providers.edit` | Edit Provider | Providers |
| `providers.delete` | Delete Provider | Providers |
| `bookings.view` | View Bookings | Bookings |
| `bookings.create` | Create Booking | Bookings |
| `bookings.edit` | Edit Booking | Bookings |
| `bookings.delete` | Delete Booking | Bookings |
| `messages.view` | View Messages | Messages |
| `messages.delete` | Delete Messages | Messages |
| `reviews.view` | View Reviews | Reviews |
| `reviews.delete` | Delete Reviews | Reviews |
| `payments.view` | View Payments | Payments |
| `payments.manage` | Manage Payments | Payments |
| `settings.manage` | Manage Settings | Settings |
| `roles.manage` | Manage Roles | Admin |
| `permissions.manage` | Manage Permissions | Admin |

### CV Builder resource mapping

This SaaS maps generic permission modules to actual Filament resources:

| Permission module | Filament resource | Description |
|-------------------|-------------------|-------------|
| Users | `UserResource` | Customer accounts |
| Providers | `TemplateResource` | CV templates catalog |
| Bookings | `PlanResource`, `SubscriptionResource` | Plans & subscriptions |
| Messages | `AiLogResource` | AI request logs |
| Reviews | `CmsPageResource` | CMS pages |
| Payments | `PaymentResource` | PayFast payments |
| Settings | `SettingResource` | App settings |
| Admin | `RoleResource`, `PermissionResource` | Role/permission CRUD |

---

## Default Roles

| Role | Slug | Access summary |
|------|------|----------------|
| Super Admin | `super-admin` | **Everything** — Gate bypass |
| Admin | `admin` | All permissions |
| Manager | `manager` | Dashboard, users (view/edit), providers, bookings, payments (view), messages (view) |
| Support | `support` | Dashboard, users (view), bookings (view), payments (view), messages (view) |
| Editor | `editor` | Dashboard, providers (view/edit), reviews (view/delete) |

### Seeded staff accounts

Password for all: `password`

| Email | Role |
|-------|------|
| superadmin@example.com | super-admin |
| admin@example.com | admin |
| manager@example.com | manager |
| support@example.com | support |
| editor@example.com | editor |

---

## Filament Admin Menu

### Navigation group: **Admin**

| Item | Permission required |
|------|---------------------|
| Roles | `roles.manage` |
| Permissions | `permissions.manage` |

### Other groups (auto-hidden by policies)

| Group | Resources | View permission |
|-------|-----------|-----------------|
| SaaS | Users, Plans, Subscriptions, Payments | Module-specific |
| Content | Templates, CMS Pages | `providers.*`, `reviews.*` |
| AI & Analytics | AI Logs | `messages.view` |
| System | Settings | `settings.manage` |

---

## Super Admin

Configured in `App\Providers\AuthServiceProvider`:

```php
Gate::before(function ($user, string $ability) {
    if ($user->hasRole('super-admin')) {
        return true;
    }
    return null;
});
```

Super Admin:

- Sees all menus
- Can access all Filament pages
- Can manage roles and permissions
- Cannot be deleted via User policy if assigned `super-admin` role

---

## Policies

| Policy | Model | Permissions used |
|--------|-------|------------------|
| `UserPolicy` | User | users.* |
| `TemplatePolicy` | Template | providers.* |
| `PlanPolicy` | Plan | bookings.* |
| `SubscriptionPolicy` | Subscription | bookings.* |
| `PaymentPolicy` | Payment | payments.view / payments.manage |
| `AiLogPolicy` | AiLog | messages.* |
| `CmsPagePolicy` | CmsPage | reviews.* |
| `SettingPolicy` | Setting | settings.manage |
| `RolePolicy` | Role | roles.manage |
| `PermissionPolicy` | Permission | permissions.manage |

Dashboard access uses `PermissionName::DashboardView` via `Dashboard::canAccess()`.

---

## Usage Examples

### Check permission in code

```php
if (auth()->user()->can('users.view')) {
    // allowed
}
```

### Check role

```php
if (auth()->user()->hasRole('manager')) {
    // allowed
}
```

### Middleware (available aliases)

```php
Route::middleware(['permission:users.view'])->group(function () {
    //
});

Route::middleware(['role:admin|manager'])->group(function () {
    //
});
```

### Assign role programmatically

```php
$user->syncRoles(['manager']);
$user->assignRole('support');
$user->removeRole('editor');
```

### Sync role permissions

```php
$role = Role::findByName('manager');
$role->syncPermissions(['users.view', 'users.edit', 'dashboard.view']);
```

---

## Files Reference

| Path | Purpose |
|------|---------|
| `app/Enums/PermissionName.php` | All permission constants |
| `app/Enums/RoleName.php` | Staff role slugs |
| `config/permissions.php` | Role matrix + resource map |
| `config/permission.php` | Spatie package config |
| `app/Policies/*` | Authorization policies |
| `app/Filament/Resources/RoleResource.php` | Role CRUD |
| `app/Filament/Resources/PermissionResource.php` | Permission CRUD |
| `app/Filament/Forms/PermissionCheckboxGroups.php` | Grouped permission UI |
| `database/seeders/RolesAndPermissionsSeeder.php` | Default seed |
| `database/migrations/*permission*` | Spatie tables |
| `database/migrations/2026_05_22_120001_remove_obsolete_admin_permissions.php` | Cleanup old permission names |

---

## Troubleshooting

### Menu not showing after role change

```bash
php artisan permission:cache-reset
# or
php artisan cache:clear
```

### Re-sync all permissions

```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

### 403 on admin page

1. Confirm user has staff role (`super-admin`, `admin`, etc.)
2. Confirm role has required permission
3. Super Admin should always work — check `Gate::before`

---

## Security Notes

- Core staff roles (`super-admin`, `admin`, `manager`, `support`, `editor`) cannot be deleted from Role resource
- Super Admin users cannot be deleted from User resource
- Prefer assigning **roles** over direct permissions for admin users
- Use `roles.manage` and `permissions.manage` only for trusted staff
