# PROJECT FULL DOCUMENTATION

**Project:** ApplyDocs ZA
**Package name:** `applydocs/applydocs-za`
**Document generated:** 2026-05-21  
**Scope:** Complete technical reference for the Laravel SaaS CV builder platform.

> **Important:** This project is an **AI-powered CV/resume builder SaaS** with subscriptions, PDF export, OpenAI features, and a Filament admin panel. It is **not** a marketplace/booking/messenger platform. Sections that reference booking, providers, messaging, OTP, or reviews are documented honestly — either mapped to the equivalent feature in this codebase or marked **Not implemented**.

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Environment Details](#2-environment-details)
3. [Composer Packages](#3-composer-packages)
4. [NPM Packages](#4-npm-packages)
5. [Database Documentation](#5-database-documentation)
6. [Module Documentation](#6-module-documentation)
7. [Route Documentation](#7-route-documentation)
8. [Controller Documentation](#8-controller-documentation)
9. [Model Documentation](#9-model-documentation)
10. [View Documentation](#10-view-documentation)
11. [Auth Flow](#11-auth-flow)
12. [Messaging Flow](#12-messaging-flow)
13. [Booking Flow](#13-booking-flow-subscription--checkout)
14. [Payment Flow](#14-payment-flow)
15. [Admin Panel](#15-admin-panel)
16. [File Structure](#16-file-structure)
17. [API / AJAX Flow](#17-api--ajax-flow)
18. [Events / Notifications](#18-events--notifications)
19. [Deployment Guide](#19-deployment-guide)
20. [Future Improvements](#20-future-improvements)

---

## 1. PROJECT OVERVIEW

### Project Name

**ApplyDocs ZA**

### Purpose

A subscription-based SaaS platform that allows users to:

- Create and edit professional CVs/resumes in a visual builder
- Choose from multiple CV templates (Classic, Modern, Professional, etc.)
- Use OpenAI-powered writing assistance (summary, experience, skills, grammar, cover letters)
- Export CVs and cover letters to PDF (via Spatie Browsershot + Puppeteer)
- Subscribe to paid plans via PayFast (South African payment gateway)
- Manage account, billing, and subscription from a customer dashboard
- Operate staff/admin functions via Filament admin panel (`/admin`)

### Main Business Flow

```
Guest visits marketing site
    → Registers / Logs in
    → Email verification (required for builder)
    → Dashboard
    → Create CV
    → CV Builder (sections + live preview + AI + PDF download)
    → Optional: upgrade plan via PayFast
    → Paid plan removes watermark + increases AI credits
    → PDF export / billing history
```

### User Types

| User Type | Description | Access |
|-----------|-------------|--------|
| **Guest** | Unauthenticated visitor | Marketing pages, auth pages |
| **Customer (End User)** | Registered user building CVs | Dashboard, builder, AI, PDF, subscription |
| **Staff (Admin roles)** | Internal team with Spatie roles | Filament admin panel `/admin` |
| **Super Admin** | Full bypass via `Gate::before` | All admin features + role management |

**Staff roles:** Super Admin, Admin, Manager, Support, Editor

> **Not in this project:** Service Provider accounts, customer–provider marketplace, interview booking between parties.

### Customer Flow (End User)

1. Visit `/` (home), `/pricing`, `/about`, etc.
2. Register at `/register` or login at `/login`
3. Verify email (`MustVerifyEmail` on `User` model)
4. Land on `/dashboard`
5. Create CV → redirected to `/cvs/{slug}/builder`
6. Edit sections (personal details, experience, education, skills, etc.)
7. Live preview updates via AJAX
8. Use AI buttons (summary, improve experience/skills, grammar fix)
9. Switch template from preview panel
10. Download PDF (instant, preview-matched)
11. Manage subscription at `/dashboard/subscription`
12. View PDF export history at `/dashboard/billing`

### Service Provider Flow

**Not implemented.** This application has no provider marketplace, provider profiles, or provider onboarding.  
**Equivalent:** Staff users (Manager, Support, Editor) manage platform content and users via Filament.

### Admin Flow

1. Staff user logs in at `/admin/login`
2. Filament dashboard shows stats widgets (users, revenue, AI usage, payments)
3. Navigate groups: **SaaS**, **Content**, **AI & Analytics**, **System**
4. CRUD managed by role permissions (Spatie Permission + policies)
5. Super Admin / Admin: full access including Roles & Permissions
6. Manager: users (view/edit), templates, plans, payments, AI logs
7. Support: users (view), payments (view), AI logs
8. Editor: templates, CMS pages

### Guest Flow

| Step | URL | Action |
|------|-----|--------|
| Browse | `/`, `/pricing`, `/about`, `/contact` | Read marketing content |
| Legal | `/privacy-policy`, `/terms` | Static pages |
| Auth | `/login`, `/register` | Must register to use builder |
| Blocked | `/dashboard`, `/cvs/*` | Redirect to login |

### Authentication Flow

```
Guest → /register (name, email, password)
     → User created
     → /verify-email prompt
     → User clicks signed link /verify-email/{id}/{hash}
     → email_verified_at set
     → Session active
     → Redirect to /dashboard (middleware: auth, verified for builder routes)
```

**Password reset:** `/forgot-password` → email token → `/reset-password/{token}`

> **OTP login:** Not implemented. Auth uses email + password (Laravel Breeze).

### Messaging Flow

**Not implemented.** No conversations, chat, or real-time messaging tables or routes exist.

### Booking Flow

**Not implemented.** No appointment/booking module.

**Equivalent:** [Subscription / Checkout Flow](#13-booking-flow-subscription--checkout)

### Payment Flow

PayFast subscription checkout — see [Section 14](#14-payment-flow).

### Notification Flow

| Channel | Status | Usage |
|---------|--------|-------|
| Email verification | Active | Breeze `MustVerifyEmail` |
| Password reset email | Active | Breeze |
| Database notifications | Not used | — |
| Push / Pusher | Not configured | `BROADCAST_CONNECTION=log` |
| Filament notifications | Available | Admin UI (Filament package) |
| Mail | Log driver (dev) | `MAIL_MAILER=log` |

### Review Flow

**Not implemented.** No reviews/ratings tables or UI.

---

## 2. ENVIRONMENT DETAILS

### Runtime Versions (detected on development machine)

| Component | Version |
|-----------|---------|
| **PHP** | 8.3.24 |
| **Laravel** | 12.60.2 (from `composer.lock`) |
| **Node.js** | 22.22.0 |
| **NPM** | 11.1.0 |
| **Filament** | 3.3.0 |

### Configuration (from `.env.example` / project defaults)

| Setting | Value | Config key / file |
|---------|-------|-------------------|
| **Database engine** | MySQL | `DB_CONNECTION=mysql` |
| **Database name** | `applydocs_za` | `DB_DATABASE` |
| **Queue driver** | Database | `QUEUE_CONNECTION=database` |
| **Cache driver** | Database | `CACHE_STORE=database` |
| **Session driver** | Database | `SESSION_DRIVER=database` |
| **Broadcasting** | Log (no realtime) | `BROADCAST_CONNECTION=log` |
| **Mail driver** | Log (dev) | `MAIL_MAILER=log` |
| **Filesystem default** | Local | `FILESYSTEM_DISK=local` |
| **Filament disk** | Public | `FILAMENT_FILESYSTEM_DISK=public` |
| **Media disk** | Public | `MEDIA_DISK=public` |
| **PDF export disk** | Local | `PDF_EXPORT_DISK=local` |
| **AI queue** | default | `AI_QUEUE=default` |
| **PDF queue** | default | `PDF_EXPORT_QUEUE=default` |
| **PayFast mode** | sandbox | `PAYFAST_MODE=sandbox` |
| **OpenAI model** | gpt-4o-mini | `OPENAI_MODEL` |

### Required External Services

| Service | Purpose | Env vars |
|---------|---------|----------|
| MySQL | Primary database | `DB_*` |
| OpenAI API | AI text generation | `OPENAI_API_KEY` |
| PayFast | Subscription payments | `PAYFAST_MERCHANT_ID`, `PAYFAST_MERCHANT_KEY`, `PAYFAST_PASSPHRASE` |
| Node.js + Puppeteer | PDF generation (Browsershot) | `BROWSERSHOT_*` |

### PHP Extensions (recommended)

`openssl`, `pdo`, `pdo_mysql`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `gd` or `imagick` (for medialibrary)

---

## 3. COMPOSER PACKAGES

Sources: `composer.json`, `composer.lock`

### Direct Production Dependencies

| Package Name | Installed Version | Purpose | Used In | Notes |
|--------------|-------------------|---------|---------|-------|
| `php` | ^8.3 (8.3.24) | Runtime | Entire app | Minimum PHP 8.3 |
| `laravel/framework` | v12.60.2 | Core framework | Entire app | Routing, Eloquent, queues, auth |
| `laravel/sanctum` | v4.3.2 | API token auth | `routes/api.php` | Minimal API: `/api/user` |
| `laravel/tinker` | v2.11.1 | REPL debugging | Dev/console | `php artisan tinker` |
| `filament/filament` | v3.3.0 | Admin panel UI | `/admin`, `app/Filament/*` | Resources, widgets, dashboard |
| `spatie/laravel-permission` | v7.4.1 | Roles & permissions | Users, Filament, policies | 5 staff roles, 9 permissions |
| `spatie/laravel-medialibrary` | v11.22.1 | File/media uploads | `User` avatar | `avatar` collection + thumb conversion |
| `spatie/laravel-activitylog` | v4.12.3 | Audit logging | `User` model | Logs name/email changes |
| `spatie/browsershot` | 5.3.0 | HTML → PDF | PDF export services | Uses headless Chrome via Puppeteer |

### Direct Development Dependencies

| Package Name | Installed Version | Purpose | Used In | Notes |
|--------------|-------------------|---------|---------|-------|
| `laravel/breeze` | v2.4.2 | Auth scaffolding | Auth controllers/views | Blade stack, email verification |
| `laravel/pint` | (lock) | Code style | Dev | `./vendor/bin/pint` |
| `laravel/pail` | (lock) | Log tailing | Dev | `composer dev` script |
| `laravel/sail` | (lock) | Docker dev | Optional | Not required for Laragon |
| `fakerphp/faker` | v1.24.1 | Test data | Factories/seeders | |
| `mockery/mockery` | 1.6.12 | Mocking | PHPUnit tests | |
| `nunomaduro/collision` | v8.9.4 | CLI error reporting | Dev | |
| `phpunit/phpunit` | 11.5.55 | Testing | `tests/` | |

### Key Transitive Dependencies (why they exist)

| Package | Version | Why It Exists |
|---------|---------|---------------|
| `livewire/livewire` | v3.8.0 | Required by Filament v3 | Admin panel reactivity |
| `guzzlehttp/guzzle` | 7.10.3 | HTTP client | OpenAI API calls, PayFast ITN validation |
| `doctrine/dbal` | 4.4.3 | Schema introspection | Filament / migrations tooling |
| `blade-ui-kit/blade-heroicons` | 2.7.0 | Heroicons | Filament navigation icons |
| `spatie/image` | 3.9.4 | Image processing | Medialibrary conversions |
| `openspout/openspout` | v4.32.0 | Spreadsheet export | Filament export actions |
| `nesbot/carbon` | 3.11.4 | Date/time | Subscriptions, plan expiry |
| `monolog/monolog` | 3.10.0 | Logging | Application logs |

### Filament Sub-packages (all v3.3.0)

| Package | Purpose |
|---------|---------|
| `filament/actions` | Table/form actions |
| `filament/forms` | Admin forms |
| `filament/tables` | Admin data tables |
| `filament/widgets` | Dashboard charts/stats |
| `filament/notifications` | Admin toast notifications |
| `filament/infolists` | Read-only detail views |
| `filament/support` | Shared Filament utilities |

---

## 4. NPM PACKAGES

Sources: `package.json`, `package-lock.json`

### Direct Dependencies

| Package | package.json | Installed (lock) | Purpose | Frontend Usage |
|---------|--------------|------------------|---------|----------------|
| `vite` | ^6.0.11 | 6.4.2 | Build tool | Bundles JS/CSS for production |
| `laravel-vite-plugin` | ^1.2.0 | 1.3.0 | Laravel ↔ Vite bridge | `@vite` in Blade layouts |
| `tailwindcss` | ^3.1.0 | 3.4.19 | Utility CSS | Marketing, dashboard, builder UI |
| `@tailwindcss/vite` | ^4.0.0 | 4.3.0 | Tailwind Vite integration | Vite config |
| `@tailwindcss/forms` | ^0.5.2 | 0.5.11 | Form styling | Input/select styling |
| `alpinejs` | ^3.4.2 | 3.15.12 | Reactive UI | CV builder (`cvBuilder` component) |
| `axios` | ^1.7.4 | 1.16.1 | HTTP client | Autosave, preview, AI, PDF download |
| `autoprefixer` | ^10.4.2 | 10.5.0 | CSS prefixes | PostCSS pipeline |
| `postcss` | ^8.4.31 | 8.5.15 | CSS processing | Tailwind build |
| `puppeteer` | ^25.0.4 | 25.0.4 | Headless Chrome | Used by Browsershot for PDF (server-side) |
| `concurrently` | ^9.0.1 | 9.2.1 | Run parallel processes | `composer dev` script |

### Not Used in This Project

| Package | Status |
|---------|--------|
| `bootstrap` | Not in package.json |
| `jquery` | Not used |
| `pusher-js` | Not installed |
| `laravel-echo` | Not installed |
| `vue` | Not used (Alpine.js instead) |
| `livewire` (npm) | Loaded via Composer/Filament, not npm |

### Frontend Entry Points

| File | Loads |
|------|-------|
| `resources/js/app.js` | Alpine.js, CV builder registration |
| `resources/js/bootstrap.js` | Axios defaults, CSRF |
| `resources/js/cv/builder.js` | Full CV builder Alpine data component |
| `resources/css/app.css` | Tailwind directives |

---

## 5. DATABASE DOCUMENTATION

### Entity Relationship Map

```
users
├── plan_id → plans
├── subscriptions (hasMany)
├── payments (hasMany)
├── cvs (hasMany)
├── cover_letters (hasMany)
├── ai_logs (hasMany)
├── pdf_exports (hasMany)
├── activities (hasMany)
├── media (Spatie morph)
└── roles/permissions (Spatie)

plans
├── users (hasMany)
├── subscriptions (hasMany)
└── payments (hasMany)

cvs
├── user_id → users
├── template_id → templates
├── sections (hasMany cv_sections)
├── cover_letters (hasMany)
└── pdf_exports (morphMany)

cover_letters
├── user_id → users
├── cv_id → cvs (nullable)
└── pdf_exports (morphMany)

payments
├── user_id → users
├── plan_id → plans
└── subscription_id → subscriptions

subscriptions
├── user_id → users
├── plan_id → plans
└── payments (hasMany)

templates
└── cvs (hasMany)

pdf_exports
├── user_id → users
└── exportable (morph: Cv | CoverLetter)

ai_logs
└── user_id → users

cms_pages (standalone)
settings (standalone)
activity_log (polymorphic log)
media (polymorphic files)
roles / permissions / pivots (Spatie)
sessions, cache, jobs, failed_jobs (Laravel infra)
personal_access_tokens (Sanctum)
```

### All Tables

#### `users`

| Attribute | Value |
|-----------|-------|
| **Purpose** | Customer accounts + staff admin users |
| **Primary key** | `id` |
| **Relations** | `belongsTo(Plan)`, `hasMany(Cv, Subscription, Payment, AiLog, PdfExport)`, Spatie roles, medialibrary |
| **Important fields** | `name`, `email`, `password`, `plan_id`, `plan_expiry`, `ai_usage_count`, `email_verified_at`, `deleted_at` |
| **Used modules** | Auth, Dashboard, Builder, AI, Subscription, Admin |

#### `plans`

| Attribute | Value |
|-----------|-------|
| **Purpose** | Subscription tiers (Free, 24h, 1 week, 1 month, 1 year, Lifetime) |
| **Primary key** | `id` |
| **Relations** | `hasMany(User, Subscription, Payment)` |
| **Important fields** | `name`, `slug`, `price`, `currency`, `ai_credits_limit`, `duration_hours/days`, `is_lifetime`, `has_watermark`, `features`, `status` |
| **Used modules** | Subscription, Pricing, Admin, AI limits |

#### `subscriptions`

| Attribute | Value |
|-----------|-------|
| **Purpose** | Active/historical user subscriptions |
| **Primary key** | `id` |
| **Relations** | `belongsTo(User, Plan)`, `hasMany(Payment)` |
| **Important fields** | `status`, `starts_at`, `ends_at`, `cancelled_at` |
| **Used modules** | Subscription, PayFast activation, Admin |

#### `payments`

| Attribute | Value |
|-----------|-------|
| **Purpose** | PayFast payment records |
| **Primary key** | `id` |
| **Relations** | `belongsTo(User, Plan, Subscription)` |
| **Important fields** | `gateway`, `transaction_id`, `reference`, `amount`, `status`, `payload` (JSON ITN data) |
| **Used modules** | PayFast, Billing, Admin |

#### `cvs`

| Attribute | Value |
|-----------|-------|
| **Purpose** | User CV documents |
| **Primary key** | `id` |
| **Route key** | `slug` |
| **Relations** | `belongsTo(User, Template)`, `hasMany(CvSection, CoverLetter)`, `morphMany(PdfExport)` |
| **Important fields** | `title`, `slug`, `json_data` (full CV document JSON), `status` (draft/published) |
| **Used modules** | Builder, Preview, PDF, AI |

#### `cv_sections`

| Attribute | Value |
|-----------|-------|
| **Purpose** | Optional normalized section storage (legacy/support) |
| **Primary key** | `id` |
| **Relations** | `belongsTo(Cv)` |
| **Important fields** | `section_key`, `sort_order`, `json_data` |
| **Used modules** | CV (primary data lives in `cvs.json_data`) |

#### `cover_letters`

| Attribute | Value |
|-----------|-------|
| **Purpose** | Cover letter documents |
| **Primary key** | `id` |
| **Route key** | `slug` |
| **Relations** | `belongsTo(User, Cv)`, `morphMany(PdfExport)` |
| **Important fields** | `title`, `slug`, `json_data`, `status` |
| **Used modules** | Dashboard, AI cover letter, PDF |

#### `templates`

| Attribute | Value |
|-----------|-------|
| **Purpose** | DB catalog of CV templates (synced with `config/cv-templates.php`) |
| **Primary key** | `id` |
| **Relations** | `hasMany(Cv)` |
| **Important fields** | `name`, `slug`, `description`, catalog/thumbnail fields |
| **Used modules** | Builder template picker, Admin |

#### `pdf_exports`

| Attribute | Value |
|-----------|-------|
| **Purpose** | Queued/historical PDF generation jobs |
| **Primary key** | `id` |
| **Relations** | `belongsTo(User)`, `morphTo(exportable)` |
| **Important fields** | `type` (cv/cover_letter), `filename`, `disk`, `path`, `status`, `options`, `file_size`, `completed_at` |
| **Used modules** | PDF export, Billing history |

#### `ai_logs`

| Attribute | Value |
|-----------|-------|
| **Purpose** | AI request audit trail |
| **Primary key** | `id` |
| **Relations** | `belongsTo(User)` |
| **Important fields** | `feature`, `model`, `tokens_used`, `credits_used`, `request`, `response`, `status`, `result`, `error_message`, `completed_at` |
| **Used modules** | AI features, Admin analytics |

#### `cms_pages`

| Attribute | Value |
|-----------|-------|
| **Purpose** | CMS content managed in admin |
| **Primary key** | `id` |
| **Important fields** | `title`, `slug`, `content`, `status`, `meta`, `published_at` |
| **Used modules** | Admin CMS (Editor role) |

#### `settings`

| Attribute | Value |
|-----------|-------|
| **Purpose** | Key-value application settings |
| **Primary key** | `id` |
| **Important fields** | `group`, `key`, `value`, `type`, `description` |
| **Used modules** | Admin System settings |

#### Spatie Permission Tables

| Table | Purpose |
|-------|---------|
| `roles` | Role definitions (super-admin, admin, manager, support, editor, user) |
| `permissions` | Permission definitions (users.view, etc.) |
| `model_has_roles` | User ↔ role pivot |
| `model_has_permissions` | Direct user permissions |
| `role_has_permissions` | Role ↔ permission pivot |

#### Spatie / Laravel Infrastructure

| Table | Purpose |
|-------|---------|
| `media` | User avatars and uploaded files |
| `activity_log` | Spatie activity log entries |
| `personal_access_tokens` | Sanctum API tokens |
| `sessions` | Database sessions |
| `cache` / `cache_locks` | Database cache |
| `jobs` / `job_batches` / `failed_jobs` | Queue processing |
| `password_reset_tokens` | Password reset |

### Key Join Examples

```sql
-- User with active plan
SELECT u.*, p.name AS plan_name
FROM users u
LEFT JOIN plans p ON p.id = u.plan_id
WHERE u.id = ?;

-- CV with owner and template
SELECT c.*, u.email, t.slug AS template_slug
FROM cvs c
JOIN users u ON u.id = c.user_id
LEFT JOIN templates t ON t.id = c.template_id
WHERE c.slug = ? AND c.user_id = ?;

-- Payment with plan for admin
SELECT pay.*, pl.name, u.email
FROM payments pay
JOIN users u ON u.id = pay.user_id
LEFT JOIN plans pl ON pl.id = pay.plan_id;
```

---

## 6. MODULE DOCUMENTATION

### Authentication

| Item | Detail |
|------|--------|
| **Purpose** | Register, login, email verification, password reset |
| **Entry point** | `/login`, `/register` |
| **Controllers** | `app/Http/Controllers/Auth/*` (Breeze) |
| **Models** | `User` |
| **Views** | `resources/views/auth/*` |
| **Routes** | `routes/auth.php` |
| **Tables** | `users`, `password_reset_tokens`, `sessions` |
| **Flow** | Guest middleware → form POST → session regenerate → redirect intended |

### Registration

| Item | Detail |
|------|--------|
| **Purpose** | Create customer account |
| **Controller** | `RegisteredUserController` |
| **Validation** | Name, email (unique), password (confirmed) |
| **Post-action** | Auto-login + redirect to dashboard or verification |

### Login

| Item | Detail |
|------|--------|
| **Purpose** | Session-based authentication |
| **Controller** | `AuthenticatedSessionController` |
| **Middleware** | `guest` for form, `auth` for logout |

### OTP

**Not implemented.** Email verification uses signed URL, not OTP codes.

### Profile

| Item | Detail |
|------|--------|
| **Purpose** | Update name, email, password, avatar photo |
| **Controller** | `ProfileController` |
| **Views** | `dashboard/profile`, `profile/edit` |
| **Routes** | `/dashboard/profile`, `/profile` (redirect) |
| **Media** | Spatie `avatar` collection on User |

### CV Builder (Core Module)

| Item | Detail |
|------|--------|
| **Purpose** | Visual CV editing with live preview |
| **Entry point** | `/cvs/{cv}/builder` |
| **Controllers** | `CvBuilderController`, `CvTemplateRenderController` |
| **Services** | `CvBuilderService`, `CvDocumentNormalizer`, `CvDocumentUpdateService`, `CvTemplateRenderer`, `TemplateRegistry` |
| **Frontend** | Alpine.js `cvBuilder` in `resources/js/cv/builder.js` |
| **Views** | `cv/builder/index`, `components/cv/builder/*` |
| **Tables** | `cvs`, `templates` |
| **Flow** | Load JSON document → edit sections → autosave PATCH → preview POST → optional PDF POST |

**Builder sections:** personal_details, experience, education, skills, languages, projects, references, certifications, custom_sections

### AI Module

| Item | Detail |
|------|--------|
| **Purpose** | OpenAI-powered CV writing assistance |
| **Entry point** | Builder AI buttons, `/ai/*` routes |
| **Controller** | `AiController` |
| **Services** | `AiService`, `OpenAiClient`, `AiPromptBuilder`, `AiUsageService`, `AiLogService` |
| **Job** | `ProcessAiRequestJob` (queued cover letter) |
| **Config** | `config/ai.php` |
| **Tables** | `ai_logs` |
| **Features** | Summary, improve experience, improve skills, grammar fix, cover letter |
| **Limits** | Per-plan `ai_credits_limit`, tracked in `users.ai_usage_count` |

### PDF Export Module

| Item | Detail |
|------|--------|
| **Purpose** | Generate downloadable PDF from CV/cover letter |
| **Controllers** | `PdfExportController` |
| **Services** | `PdfExportService`, `CvPdfGenerator`, `PdfBrowsershotService`, `PdfWatermarkService` |
| **Job** | `GeneratePdfExportJob` (queued path) |
| **Instant path** | `POST /cvs/{cv}/export/pdf/download` (preview-matched sync download) |
| **Tables** | `pdf_exports` |
| **Tech** | Spatie Browsershot + Puppeteer, A4, zero-margin preview match |

### Subscription Module

| Item | Detail |
|------|--------|
| **Purpose** | Plan selection, upgrade, downgrade, expiry |
| **Controllers** | `SubscriptionController` |
| **Services** | `SubscriptionService`, `PlanService` |
| **Command** | `subscriptions:expire` (hourly cron) |
| **Tables** | `plans`, `subscriptions`, `users.plan_id/plan_expiry` |
| **Views** | `dashboard/subscription`, `components/dashboard/plans-grid` |

### Payments (PayFast)

| Item | Detail |
|------|--------|
| **Purpose** | Process subscription payments |
| **Controllers** | `PayFastController` (ITN webhook), `SubscriptionController` (checkout) |
| **Services** | `PayFastService`, `PaymentService` |
| **Config** | `config/payfast.php` |
| **Tables** | `payments` |

### Templates Module

| Item | Detail |
|------|--------|
| **Purpose** | 10 plug-and-play CV layouts |
| **Config registry** | `config/cv-templates.php` |
| **Renderer** | `CvTemplateRenderer`, `CvJsonRenderer`, `CvPreviewTransformer` |
| **Views** | `resources/views/cv/templates/*`, `components/cv/sections/*` |
| **Templates** | classic, modern, professional, minimal, creative, executive, compact, elegant, bold, sidebar |

### Dashboard Module

| Item | Detail |
|------|--------|
| **Purpose** | Customer hub |
| **Controller** | `DashboardController` |
| **Pages** | Overview, CVs list, cover letters, subscription, billing, profile |
| **Layout** | `components/dashboard/layout` with sidebar navbar |

### Admin (Filament)

| Item | Detail |
|------|--------|
| **Purpose** | Staff back-office |
| **Panel** | `/admin` via `AdminPanelProvider` |
| **Resources** | Users, Plans, Subscriptions, Payments, Templates, AI Logs, CMS Pages, Settings, Roles, Permissions |
| **Widgets** | StatsOverview, PaymentsChart, UserGrowthChart, AiUsageChart, LatestPaymentsTable |
| **Auth** | Filament login + `User::canAccessPanel()` + Spatie roles |

### Modules NOT in Project

| Module | Status |
|--------|--------|
| Provider System | Not implemented |
| Booking | Not implemented |
| Reviews | Not implemented |
| Messenger | Not implemented |
| Availability | Not implemented |
| Qualifications / Achievements / Gallery | Not as separate modules (CV sections only) |
| Reports (custom) | Partial via Filament widgets only |

---

## 7. ROUTE DOCUMENTATION

### Web Routes — Public (Guest)

| Route | Method | Controller | Middleware | Auth? | Purpose |
|-------|--------|------------|------------|-------|---------|
| `/` | GET | `MarketingController@home` | web | No | Home page |
| `/pricing` | GET | `MarketingController@pricing` | web | No | Pricing page |
| `/contact` | GET | `MarketingController@contact` | web | No | Contact page |
| `/about` | GET | `MarketingController@about` | web | No | About page |
| `/privacy-policy` | GET | `MarketingController@privacy` | web | No | Privacy policy |
| `/terms` | GET | `MarketingController@terms` | web | No | Terms |
| `/register` | GET/POST | `RegisteredUserController` | guest | No | Registration |
| `/login` | GET/POST | `AuthenticatedSessionController` | guest | No | Login |
| `/forgot-password` | GET/POST | `PasswordResetLinkController` | guest | No | Request reset |
| `/reset-password/{token}` | GET/POST | `NewPasswordController` | guest | No | Reset password |
| `/payfast/itn` | POST | `PayFastController@itn` | web (CSRF exempt) | No | PayFast webhook |

### Web Routes — Authenticated Customer

| Route | Method | Controller | Middleware | Auth? | Purpose |
|-------|--------|------------|------------|-------|---------|
| `/dashboard` | GET | `DashboardController@index` | auth, verified | Yes | Dashboard home |
| `/dashboard/cvs` | GET | `DashboardController@cvs` | auth, verified | Yes | CV list |
| `/dashboard/cover-letters` | GET | `DashboardController@coverLetters` | auth, verified | Yes | Cover letters |
| `/dashboard/subscription` | GET | `DashboardController@subscription` | auth, verified | Yes | Plans |
| `/dashboard/billing` | GET | `DashboardController@billing` | auth, verified | Yes | PDF history |
| `/dashboard/profile` | GET | `DashboardController@profile` | auth, verified | Yes | Profile |
| `/cvs` | POST | `CvController@store` | auth, verified | Yes | Create CV |
| `/cvs/{cv}/builder` | GET | `CvBuilderController@edit` | auth, verified | Yes | Open builder |
| `/cvs/{cv}/builder` | PUT | `CvBuilderController@update` | auth, verified | Yes | Save/publish |
| `/cvs/{cv}/builder/autosave` | PATCH | `CvBuilderController@autosave` | auth, verified | Yes | Autosave draft |
| `/cvs/{cv}/render` | POST | `CvTemplateRenderController@live` | auth, verified | Yes | Live preview HTML |
| `/cvs/{cv}/render/{template?}` | GET | `CvTemplateRenderController@show` | auth, verified | Yes | Render saved CV |
| `/cvs/{cv}/template` | POST | `CvTemplateRenderController@switchTemplate` | auth, verified | Yes | Switch template |
| `/cvs/{cv}/export/pdf` | POST | `PdfExportController@storeCv` | auth, verified | Yes | Queue PDF export |
| `/cvs/{cv}/export/pdf/download` | POST | `PdfExportController@downloadCv` | auth, verified | Yes | Instant PDF download |
| `/cover-letters/{coverLetter}/export/pdf` | POST | `PdfExportController@storeCoverLetter` | auth, verified | Yes | Queue cover letter PDF |
| `/pdf-exports/{pdfExport}` | GET | `PdfExportController@show` | auth, verified | Yes | Export status JSON |
| `/pdf-exports/{pdfExport}/download` | GET | `PdfExportController@download` | auth, verified | Yes | Download queued PDF |
| `/ai/usage` | GET | `AiController@usage` | auth, verified | Yes | AI credit snapshot |
| `/ai/logs/{aiLog}` | GET | `AiController@show` | auth, verified | Yes | Poll AI job status |
| `/ai/summary` | POST | `AiController@generateSummary` | auth, verified | Yes | AI summary |
| `/ai/experience/improve` | POST | `AiController@improveExperience` | auth, verified | Yes | AI experience |
| `/ai/skills/improve` | POST | `AiController@improveSkills` | auth, verified | Yes | AI skills |
| `/ai/grammar-fix` | POST | `AiController@grammarFix` | auth, verified | Yes | AI grammar |
| `/ai/cover-letter` | POST | `AiController@generateCoverLetter` | auth, verified | Yes | AI cover letter (queued) |
| `/cv-templates` | GET | `CvTemplateRenderController@catalog` | auth, verified | Yes | Template catalog JSON |
| `/subscription/checkout/{plan}` | POST | `SubscriptionController@checkout` | auth, verified | Yes | Start PayFast checkout |
| `/subscription/success` | GET | `SubscriptionController@success` | auth, verified | Yes | Return URL success |
| `/subscription/cancel` | GET | `SubscriptionController@cancel` | auth, verified | Yes | Return URL cancel |
| `/profile` | PATCH/DELETE | `ProfileController` | auth | Yes | Profile update/delete |
| `/profile/photo` | POST/DELETE | `ProfileController` | auth | Yes | Avatar upload/remove |
| `/logout` | POST | `AuthenticatedSessionController@destroy` | auth | Yes | Logout |

### Auth / Verification Routes

| Route | Method | Middleware | Purpose |
|-------|--------|------------|---------|
| `/verify-email` | GET | auth | Verification notice |
| `/verify-email/{id}/{hash}` | GET | auth, signed, throttle | Verify email |
| `/email/verification-notification` | POST | auth, throttle | Resend verification |
| `/confirm-password` | GET/POST | auth | Confirm password |
| `/password` | PUT | auth | Update password |

### API Routes

| Route | Method | Middleware | Purpose |
|-------|--------|------------|---------|
| `/api/user` | GET | auth:sanctum | Return authenticated user JSON |

### Admin Routes (Filament — auto-registered)

| Prefix | Purpose |
|--------|---------|
| `/admin` | Filament dashboard |
| `/admin/login` | Admin login |
| `/admin/users` | User management |
| `/admin/plans` | Plan management |
| `/admin/subscriptions` | Subscriptions |
| `/admin/payments` | Payments |
| `/admin/templates` | Template catalog |
| `/admin/ai-logs` | AI logs |
| `/admin/cms-pages` | CMS pages |
| `/admin/settings` | App settings |
| `/admin/roles` | Role management |
| `/admin/permissions` | Permission list |

**Filament middleware:** EncryptCookies, StartSession, VerifyCsrfToken, `Authenticate` (Filament auth)

### Messenger Routes

**Not implemented.**

### Spatie Middleware Aliases (available, not widely applied to web routes)

| Alias | Class | Purpose |
|-------|-------|---------|
| `role` | `RoleMiddleware` | Require role |
| `permission` | `PermissionMiddleware` | Require permission |
| `role_or_permission` | `RoleOrPermissionMiddleware` | Role or permission |

Filament uses **policies** instead of route middleware for authorization.

---

## 8. CONTROLLER DOCUMENTATION

### `MarketingController`

| Method | Purpose | Input | Output | Views |
|--------|---------|-------|--------|-------|
| `home()` | Landing page | — | HTML | `pages/home` |
| `pricing()` | Pricing | — | HTML | `pages/pricing` |
| `about()` | About | — | HTML | `pages/about` |
| `contact()` | Contact | — | HTML | `pages/contact` |
| `privacy()` | Privacy | — | HTML | `pages/privacy-policy` |
| `terms()` | Terms | — | HTML | `pages/terms` |

### `DashboardController`

| Method | Purpose | Dependencies | Views |
|--------|---------|--------------|-------|
| `index()` | Dashboard stats | User, CVs, subscriptions | `dashboard/index` |
| `cvs()` | List CVs | CvRepository | `dashboard/cvs/index` |
| `coverLetters()` | List cover letters | User relation | `dashboard/cover-letters/index` |
| `subscription()` | Plans + current plan | SubscriptionService, PlanService | `dashboard/subscription/index` |
| `billing()` | PDF export history | PdfExportService | `dashboard/billing/index` |
| `profile()` | Profile page | User | `dashboard/profile/index` |

### `CvController`

| Method | Purpose | Input | Output |
|--------|---------|-------|--------|
| `store()` | Create new CV | title (optional) | Redirect to builder |

**Dependencies:** `CvBuilderService`, `CvRepositoryInterface`  
**Policy:** Authorize create on Cv

### `CvBuilderController`

| Method | Purpose | Input | Output |
|--------|---------|-------|--------|
| `edit()` | Show builder | Cv route binding | Blade + builder payload |
| `update()` | Save/publish | title, json_data, publish flag | JSON with cv meta |
| `autosave()` | Debounced save | title, json_data | JSON with cv meta |

**Dependencies:** `CvBuilderService`, `CvDocumentUpdateService`  
**Models:** `Cv`

### `CvTemplateRenderController`

| Method | Purpose | Input | Output |
|--------|---------|-------|--------|
| `show()` | Render saved CV HTML | cv, optional template | HTML |
| `live()` | Live preview from form data | json_data, template | HTML |
| `switchTemplate()` | Change template + save | template, json_data | JSON + html |
| `catalog()` | List templates | — | JSON templates array |

### `PdfExportController`

| Method | Purpose | Input | Output |
|--------|---------|-------|--------|
| `downloadCv()` | Instant preview-matched PDF | json_data, template, title | PDF binary |
| `storeCv()` | Queue PDF job | template (optional) | JSON 202 + export id |
| `storeCoverLetter()` | Queue cover letter PDF | — | JSON 202 |
| `show()` | Poll export status | pdfExport id | JSON export payload |
| `download()` | Download completed file | pdfExport id | Streamed PDF |

### `AiController`

| Method | Purpose | Input | Output |
|--------|---------|-------|--------|
| `usage()` | Credit snapshot | — | JSON usage |
| `show()` | AI log status | aiLog id | JSON log + usage |
| `generateSummary()` | AI summary | personal_details, experience, skills | JSON log |
| `improveExperience()` | AI experience | experience fields | JSON log |
| `improveSkills()` | AI skills | skills array | JSON log |
| `grammarFix()` | AI grammar | text | JSON log |
| `generateCoverLetter()` | AI cover letter | job/candidate data | JSON 202 (queued) |

**Dependencies:** `AiService`, `AiUsageService`  
**Errors:** 429 limit exceeded, 502 AI failure, 500 general

### `SubscriptionController`

| Method | Purpose | Output |
|--------|---------|--------|
| `checkout()` | Initiate PayFast payment | Redirect view with auto-submit form |
| `success()` | User return after payment | Redirect + flash message |
| `cancel()` | User cancelled payment | Redirect + flash message |

### `PayFastController`

| Method | Purpose | Input | Output |
|--------|---------|-------|--------|
| `itn()` | Instant Transaction Notification webhook | PayFast POST payload | `OK` / error text |

**Flow:** Verify signature → find payment by reference → mark completed → activate subscription

### `ProfileController`

| Method | Purpose |
|--------|---------|
| `update()` | Update name/email |
| `updatePhoto()` | Upload avatar via medialibrary |
| `destroyPhoto()` | Remove avatar |
| `destroy()` | Delete account |

### Auth Controllers (Breeze)

Standard Laravel Breeze controllers in `app/Http/Controllers/Auth/` — session registration, login, verification, password reset.

---

## 9. MODEL DOCUMENTATION

### `User`

| Attribute | Detail |
|-----------|--------|
| **Table** | `users` |
| **Traits** | HasApiTokens, HasFactory, HasRoles, InteractsWithMedia, LogsActivity, Notifiable, SoftDeletes |
| **Fillable** | name, email, password, plan_id, plan_expiry, ai_usage_count |
| **Casts** | email_verified_at (datetime), password (hashed), plan_expiry (datetime), ai_usage_count (integer) |
| **Relations** | plan, subscriptions, cvs, coverLetters, payments, aiLogs, activities, pdfExports |
| **Helpers** | `activeSubscription()`, `canAccessPanel()`, `avatar_url`, `initials` |
| **Interfaces** | FilamentUser, HasMedia, MustVerifyEmail |

### `Cv`

| Fillable | user_id, template_id, title, slug, json_data, status |
| Relations | user, template, sections, coverLetters, pdfExports |
| Casts | json_data (array), status (CvStatus enum) |
| Route key | slug |

### `CoverLetter`

| Fillable | user_id, cv_id, title, slug, json_data, status |
| Relations | user, cv, pdfExports |
| Route key | slug |

### `Plan`

| Key methods | `isFree()`, pricing helpers |
| Relations | users, subscriptions, payments |
| Used for | AI limits, watermark flag, subscription duration |

### `Subscription`

| Casts | status (SubscriptionStatus), starts_at, ends_at, cancelled_at |
| Relations | user, plan, payments |

### `Payment`

| Casts | status (PaymentStatus), payload (array) |
| Relations | user, plan, subscription |

### `Template`

| Purpose | DB mirror of template registry |
| Relations | cvs |

### `PdfExport`

| Casts | type (PdfExportType), status (PdfExportStatus), options (array) |
| Relations | user, exportable (morph) |
| Methods | `isDownloadable()` |

### `AiLog`

| Casts | status (AiLogStatus), request/response (array), completed_at |
| Relations | user |

### `CmsPage` / `Setting`

Admin-managed content and key-value settings.

### `Activity`

Spatie activity log model wrapper.

### Policies (Authorization)

| Policy | Model | Key abilities |
|--------|-------|---------------|
| UserPolicy | User | viewAny, create, update, delete (permission-based) |
| CvPolicy | Cv | Owner-based view/update/delete |
| CoverLetterPolicy | CoverLetter | Owner-based |
| PdfExportPolicy | PdfExport | Owner view/download |
| AiLogPolicy | AiLog | Owner view + ai.manage for admin |
| PlanPolicy | Plan | plans.manage |
| PaymentPolicy | Payment | payments.view / plans.manage |
| SubscriptionPolicy | Subscription | payments.view / plans.manage |
| TemplatePolicy | Template | templates.manage |
| CmsPagePolicy | CmsPage | cms.manage |
| SettingPolicy | Setting | super-admin/admin only |
| RolePolicy | Role | Super admin for mutations |
| PermissionPolicy | Permission | Super admin view only |

---

## 10. VIEW DOCUMENTATION

### Layout Hierarchy

```
layouts/marketing.blade.php     → Public marketing pages
layouts/guest.blade.php         → Auth pages (login/register)
layouts/app.blade.php           → Legacy Breeze layout
components/dashboard/layout     → Customer dashboard shell
cv/builder/index                → Standalone builder (full page)
cv/templates/wrapper            → CV preview/PDF HTML document
```

### Frontend / Marketing Pages

| View | Route | Purpose |
|------|-------|---------|
| `pages/home` | `/` | Hero, features, templates preview |
| `pages/pricing` | `/pricing` | Plan cards |
| `pages/about` | `/about` | About content |
| `pages/contact` | `/contact` | Contact form |
| `pages/privacy-policy` | `/privacy-policy` | Legal |
| `pages/terms` | `/terms` | Legal |

### Customer / Dashboard Pages

| View | Route |
|------|-------|
| `dashboard/index` | `/dashboard` |
| `dashboard/cvs/index` | `/dashboard/cvs` |
| `dashboard/cover-letters/index` | `/dashboard/cover-letters` |
| `dashboard/subscription/index` | `/dashboard/subscription` |
| `dashboard/billing/index` | `/dashboard/billing` |
| `dashboard/profile/index` | `/dashboard/profile` |

### CV Builder Components

| Component | Purpose |
|-----------|---------|
| `components/cv/builder/layout` | Full builder shell + Alpine init |
| `components/cv/builder/toolbar` | Title, save, publish, download PDF |
| `components/cv/builder/section-nav` | Section tabs |
| `components/cv/builder/forms` | All section forms + AI buttons |
| `components/cv/builder/preview` | Template picker + iframe preview |
| `components/cv/sections/*` | Rendered CV section partials |

### Auth Pages

| View | URL |
|------|-----|
| `auth/login` | `/login` |
| `auth/register` | `/register` |
| `auth/forgot-password` | `/forgot-password` |
| `auth/reset-password` | `/reset-password/{token}` |
| `auth/verify-email` | `/verify-email` |

### Admin Pages

Filament renders admin UI dynamically — no custom Blade admin views except Filament overrides in `app/Filament/`.

### PDF Views

| View | Purpose |
|------|---------|
| `cv/templates/wrapper` | Full HTML document for preview/export |
| `cv/templates/partials/styles` | Template CSS (preview/export/pdf modes) |
| `pdf/partials/header` | Optional PDF header (queued exports) |
| `pdf/partials/footer` | Optional PDF footer |
| `pdf/cover-letter` | Cover letter PDF layout |

### Rendering Flow (CV Preview)

```
Builder form (Alpine document state)
    → POST /cvs/{cv}/render (json_data + template)
    → CvTemplateRenderController@live
    → CvDocumentNormalizer
    → CvPreviewTransformer
    → CvTemplateRenderer (mode: preview)
    → cv.templates.wrapper Blade
    → HTML returned to iframe srcdoc
```

### Rendering Flow (PDF Download)

```
Builder exportPdf()
    → POST /cvs/{cv}/export/pdf/download (same payload as preview)
    → CvPdfGenerator::renderHtmlFromData (mode: export)
    → PdfBrowsershotService (zero margins, no header/footer)
    → PDF binary → browser download
```

### Messenger Pages

**Not implemented.**

---

## 11. AUTH FLOW

### Standard Auth (Implemented)

```
┌─────────┐    register/login     ┌──────────────┐
│  Guest  │ ───────────────────►  │ Authenticated │
└─────────┘                       └──────┬───────┘
                                         │
                         email verified? │
                    ┌────────────────────┴────────────────────┐
                    NO                                          YES
                    ▼                                           ▼
           /verify-email                              /dashboard, /cvs/*
           resend available                           builder (auth+verified)
```

### Session Lifecycle

1. `POST /login` → credentials validated
2. Session regenerated (Breeze)
3. `remember_me` optional cookie
4. Redirect `intended()` or `/dashboard`
5. `POST /logout` → session invalidated

### Email Verification

- Required for builder routes (`verified` middleware)
- Signed URL with throttle (6/min)
- User model implements `MustVerifyEmail`

### Admin Auth Flow

```
Staff user → /admin/login
          → Filament Authenticate middleware
          → User::canAccessPanel() checks staff role
          → Filament dashboard
```

**Seeded staff accounts (password: `password`):**

| Email | Role |
|-------|------|
| superadmin@example.com | super-admin |
| admin@example.com | admin |
| manager@example.com | manager |
| support@example.com | support |
| editor@example.com | editor |

### OTP / Provider Interview Flow

**Not implemented in this project.**

---

## 12. MESSAGING FLOW

**Not implemented.**

This project contains no:

- `conversations` or `messages` tables
- Chat controllers or views
- Pusher/Echo realtime messaging
- Unread message counters

If messaging were added in future, recommended integration point: Laravel Reverb/Pusher + dedicated messenger module.

---

## 13. BOOKING FLOW (Subscription / Checkout)

> Mapped from generic "Booking flow" to this project's **subscription purchase flow**.

```
Customer on /dashboard/subscription
    → Views plans grid (Free, 24 Hours, 1 Week, 1 Month, 1 Year, Lifetime)
    → POST /subscription/checkout/{plan}
    → SubscriptionController@checkout
         ├─ Free plan → downgradeToFree() → redirect success
         ├─ Same paid plan (not expired) → info message
         └─ Paid plan → PaymentService::createPending()
                      → PayFastService::buildPaymentForm()
                      → subscription.payfast-redirect view (auto POST to PayFast)
    → User pays on PayFast hosted page
    → PayFast POST /payfast/itn (webhook)
    → PayFastController@itn
         → verify signature
         → mark payment COMPLETE
         → SubscriptionService::activatePlan()
         → user.plan_id updated, plan_expiry set, watermark removed
    → User hits /subscription/success (return URL)
    → Dashboard shows active plan
```

### Subscription Expiry (Cron)

```
Hourly: php artisan subscriptions:expire
    → Finds expired subscriptions
    → Downgrades users to Free plan
    → Updates subscription status
```

---

## 14. PAYMENT FLOW

### Payment Gateway

| Gateway | Status | Region |
|---------|--------|--------|
| **PayFast** | Implemented | South Africa (ZAR) |

**Not implemented:** Stripe, PayPal, M-Pesa, etc.

### Checkout Request Flow

1. User selects plan on dashboard
2. Form POST to `/subscription/checkout/{plan}`
3. `PaymentService` creates pending `payments` row with unique `reference`
4. `PayFastService` builds signed form fields:
   - merchant_id, merchant_key
   - amount, item_name
   - return_url, cancel_url, notify_url (ITN)
   - m_payment_id = payment reference
5. Browser auto-submits to PayFast `process_url`

### Callback / ITN (Webhook)

| Step | Detail |
|------|--------|
| URL | `POST /payfast/itn` |
| CSRF | Exempt in `bootstrap/app.php` |
| Verify | `PayFastService::verifyItn()` — signature + optional server validation |
| COMPLETE | Mark payment completed, activate plan |
| FAILED/CANCELLED | Mark payment failed |
| Response | Plain text `OK` / error code |

### Success / Cancel (User Return URLs)

| URL | Route | User experience |
|-----|-------|-----------------|
| Return | `/subscription/success` | Flash: payment received, activation pending ITN |
| Cancel | `/subscription/cancel` | Flash: payment cancelled |

### Payment States

| Status | Meaning |
|--------|---------|
| pending | Checkout initiated |
| completed | ITN confirmed COMPLETE |
| failed | ITN FAILED/CANCELLED |

---

## 15. ADMIN PANEL

### Access

- **URL:** `/admin`
- **Login:** `/admin/login`
- **Framework:** Filament v3.3
- **Provider:** `App\Providers\Filament\AdminPanelProvider`

### Roles

| Role | Label | Panel access |
|------|-------|--------------|
| super-admin | Super Admin | Yes — bypasses all gates |
| admin | Admin | Yes — all permissions |
| manager | Manager | Yes — limited permissions |
| support | Support | Yes — view users, payments, AI |
| editor | Editor | Yes — templates, CMS |

### Permissions

| Permission | Description |
|------------|-------------|
| users.view | View users in admin |
| users.create | Create users |
| users.edit | Edit users |
| users.delete | Delete users |
| templates.manage | Manage CV templates |
| plans.manage | Manage plans/subscriptions/payments write |
| payments.view | View payments |
| ai.manage | View AI logs |
| cms.manage | Manage CMS pages |

### Navigation Groups

| Group | Resources |
|-------|-----------|
| **SaaS** | Users, Plans, Subscriptions, Payments |
| **Content** | Templates, CMS Pages |
| **AI & Analytics** | AI Logs |
| **System** | Settings, Roles, Permissions |

### Dashboard Widgets

| Widget | Data |
|--------|------|
| StatsOverview | User count, revenue, subscriptions, AI usage |
| PaymentsChart | Revenue over time |
| UserGrowthChart | New users over time |
| AiUsageChart | AI requests over time |
| LatestPaymentsTable | Recent payments table |

### Authorization

- Policies registered in `AuthServiceProvider`
- `Gate::before` grants super-admin full access
- Filament auto-hides navigation/actions based on policy `viewAny`, `create`, etc.

---

## 16. FILE STRUCTURE

```
applydocs-za/
├── app/
│   ├── Console/Commands/          # Artisan commands (subscriptions:expire)
│   ├── Contracts/                   # Interfaces (CvRepository, OpenAiClient)
│   ├── DTOs/                        # Data transfer objects (Cv, Pdf)
│   ├── Enums/                       # Status enums, roles, permissions
│   ├── Exceptions/                  # AI exceptions
│   ├── Filament/                    # Admin panel (Resources, Widgets, Pages)
│   ├── Http/
│   │   ├── Controllers/             # Web, Auth, AI, CV, PDF, Subscription
│   │   └── Requests/                # Form request validation
│   ├── Jobs/                        # Queue jobs (PDF, AI)
│   ├── Models/                      # Eloquent models
│   ├── Policies/                    # Authorization policies
│   ├── Providers/                   # Service providers (App, Auth, Filament)
│   ├── Repositories/                # CV repository
│   ├── Services/                    # Business logic layer
│   │   ├── Ai/                      # OpenAI integration
│   │   ├── Cv/                      # Builder, normalizer, templates
│   │   ├── Pdf/                     # PDF generation
│   │   └── Subscription/            # PayFast, plans, subscriptions
│   └── Support/                     # Helpers (CvDocumentMigrator)
├── bootstrap/
│   ├── app.php                      # Middleware, routing, CSRF exceptions
│   └── providers.php                # Provider registration
├── config/                          # App, AI, CV, PDF, PayFast, permissions, etc.
├── database/
│   ├── factories/                   # Model factories
│   ├── migrations/                  # Schema migrations
│   └── seeders/                     # Roles, plans, templates, CMS seeders
├── public/                          # Web root (index.php, build assets)
├── resources/
│   ├── css/                         # Tailwind app.css
│   ├── js/                          # Alpine, axios, CV builder
│   └── views/                       # Blade templates
│       ├── auth/                    # Login/register
│       ├── components/              # Reusable Blade components
│       ├── cv/                      # Builder + template renderer
│       ├── dashboard/               # Customer dashboard pages
│       ├── layouts/                 # Base layouts
│       ├── pages/                   # Marketing pages
│       ├── partials/                # Marketing sections
│       ├── pdf/                     # PDF partials
│       ├── profile/                 # Profile forms
│       └── subscription/            # PayFast redirect form
├── routes/
│   ├── web.php                      # Main web routes
│   ├── auth.php                     # Authentication routes
│   ├── api.php                      # Sanctum API
│   └── console.php                  # Scheduled tasks
├── storage/
│   ├── app/                         # Private files, PDF exports
│   ├── framework/                   # Cache, sessions, views
│   └── logs/                        # Application logs
├── tests/                           # PHPUnit tests
├── vendor/                          # Composer dependencies
├── composer.json / composer.lock
├── package.json / package-lock.json
├── vite.config.js
└── .env / .env.example
```

### Folder Purposes

| Folder | Purpose |
|--------|---------|
| `app/Services` | Core business logic — keeps controllers thin |
| `app/Filament` | Admin CRUD UI — auto-discovered resources |
| `resources/views/cv/templates` | Print/preview HTML — shared by preview and PDF |
| `storage/app/pdf-exports` | Generated PDF files (local disk) |
| `config/cv-templates.php` | Template registry (plug-and-play layouts) |

---

## 17. API / AJAX FLOW

All builder/AI endpoints use **session auth** (web middleware + axios CSRF), not Sanctum tokens.

### CV Builder AJAX

| Endpoint | Method | Request body | Success response | Errors |
|----------|--------|--------------|------------------|--------|
| `/cvs/{cv}/builder/autosave` | PATCH | `{ title, json_data }` | `{ cv: { id, slug, urls... } }` | 422 validation |
| `/cvs/{cv}/builder` | PUT | `{ title, json_data, publish }` | `{ cv, message }` | 422 validation |
| `/cvs/{cv}/render` | POST | `{ title, json_data, template }` | HTML string | 422 validation |
| `/cvs/{cv}/template` | POST | `{ template, json_data? }` | `{ html, template, cv }` | 422/403 |
| `/cvs/{cv}/export/pdf/download` | POST | `{ title, json_data, template }` | PDF binary blob | 500 JSON if error |

### AI AJAX

| Endpoint | Method | Request | Success | Errors |
|----------|--------|---------|---------|--------|
| `/ai/usage` | GET | — | `{ usage: { used, limit, remaining } }` | — |
| `/ai/summary` | POST | personal_details, experience, skills | `{ log, usage }` | 429, 502, 500 |
| `/ai/experience/improve` | POST | company, position, description... | `{ log, usage }` | 429, 502 |
| `/ai/skills/improve` | POST | `{ skills: [] }` | `{ log, usage }` | 429, 502 |
| `/ai/grammar-fix` | POST | `{ text }` | `{ log, usage }` | 429, 502 |
| `/ai/cover-letter` | POST | job/candidate payload | `{ log, usage }` 202 | 429, 502 |
| `/ai/logs/{id}` | GET | — | `{ log, usage }` | 403/404 |

### PDF Queue AJAX (legacy/history path)

| Endpoint | Method | Response |
|----------|--------|----------|
| `/cvs/{cv}/export/pdf` | POST | `{ export: { id, status } }` 202 |
| `/pdf-exports/{id}` | GET | `{ export: { status, download_url } }` |
| `/pdf-exports/{id}/download` | GET | PDF file stream |

### Validation

Form requests in `app/Http/Requests/` — e.g. `RenderCvPreviewRequest`, `GenerateSummaryRequest`, `SaveCvBuilderRequest`.

### Axios Setup

`resources/js/bootstrap.js` sets:
- `X-Requested-With: XMLHttpRequest`
- `X-CSRF-TOKEN` from meta tag

---

## 18. EVENTS / NOTIFICATIONS

### Broadcasting

| Setting | Value |
|---------|-------|
| Driver | `log` (default) |
| Pusher | Not configured |
| Laravel Echo | Not installed |
| Realtime updates | **Not used** |

### Application Events / Jobs

| Component | Type | Purpose |
|-----------|------|---------|
| `GeneratePdfExportJob` | Queued job | Async PDF generation |
| `ProcessAiRequestJob` | Queued job | Async cover letter AI |
| `ExpireSubscriptionsCommand` | Scheduled command | Hourly subscription expiry |

### Notifications

| Type | Implementation |
|------|----------------|
| Email verification | Laravel built-in notification |
| Password reset | Laravel built-in notification |
| Filament notifications | In-admin toasts (Filament package) |
| Database notifications | Not implemented |
| SMS / Push | Not implemented |

### Activity Log

Spatie Activity Log records changes to User name/email (admin audit trail via `activity_log` table).

---

## 19. DEPLOYMENT GUIDE

### Server Requirements

- PHP 8.3+
- MySQL 8+ (or MariaDB equivalent)
- Node.js 18+ (for Vite build + Puppeteer/Browsershot)
- Chrome/Chromium (bundled via Puppeteer npm package)
- Composer 2.x
- Supervisor or systemd for queue worker (recommended)

### Initial Setup

```bash
# Clone / upload project
cd applydocs-za

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Environment
cp .env.example .env
php artisan key:generate

# Configure .env: DB_*, OPENAI_API_KEY, PAYFAST_*, APP_URL

# Database
php artisan migrate --force
php artisan db:seed --force

# Frontend build
npm ci
npm run build

# Storage
php artisan storage:link

# Cache optimization (production)
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Queue Worker

Required for:
- Queued PDF exports (`GeneratePdfExportJob`)
- Queued AI cover letters (`ProcessAiRequestJob`)

```bash
php artisan queue:work --queue=default --tries=3 --timeout=180
```

Or use Supervisor with `queue:work`.

### Scheduler (Cron)

Add to crontab:

```cron
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

Scheduled tasks:
- `subscriptions:expire` — hourly
- `queue:prune-batches` — daily
- `model:prune` — daily

### File Permissions

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Browsershot / PDF on Linux Server

Ensure Node is in PATH or set in `.env`:

```
BROWSERSHOT_NODE_BINARY=/usr/bin/node
BROWSERSHOT_NPM_BINARY=/usr/bin/npm
```

Install Puppeteer dependencies (varies by OS) — see Spatie Browsershot docs.

### Production `.env` Checklist

| Variable | Production value |
|----------|------------------|
| `APP_ENV` | production |
| `APP_DEBUG` | false |
| `APP_URL` | https://yourdomain.com |
| `QUEUE_CONNECTION` | database or redis |
| `MAIL_MAILER` | smtp (real mailer) |
| `PAYFAST_MODE` | live |
| `OPENAI_API_KEY` | production key |

### Post-Deploy

```bash
php artisan optimize:clear   # if needed after deploy
php artisan migrate --force
php artisan queue:restart
```

---

## 20. FUTURE IMPROVEMENTS

### Performance

| Area | Suggestion |
|------|------------|
| PDF generation | Move to dedicated queue worker server with Chrome |
| Preview rendering | Cache rendered HTML per document hash |
| Database | Add Redis for cache/session/queue at scale |
| AI requests | Rate limit per IP + per user |

### Caching

| Target | Strategy |
|--------|----------|
| Template catalog | Config cache (`config:cache`) |
| Plan listing | Cache plans query (1 hour TTL) |
| CMS pages | Full-page cache for published pages |

### Optimization

- Eager load relations on dashboard lists
- Index `ai_logs.status` for polling queries
- Compress PDF storage or move to S3 (`PDF_EXPORT_DISK=s3`)

### Security

- Enable HTTPS everywhere
- Rotate PayFast passphrase regularly
- Rate limit AI endpoints (already partial via credit limits)
- 2FA for admin panel (Filament plugin)
- Audit log review workflow for staff actions

### Scaling

| Component | Scale approach |
|-----------|----------------|
| Web | Horizontal scaling behind load balancer (shared sessions via Redis) |
| Queue | Multiple queue workers, separate `pdf` queue |
| Storage | S3 for PDF exports and media |
| AI | Queue all AI features, add retry/backoff |

### Feature Gaps (from generic template)

These were **not** in scope but could be future modules:

- In-app messaging / support chat
- Provider marketplace / booking
- OTP-based login
- Reviews and ratings
- Stripe international payments
- Real-time notifications (Pusher/Reverb)
- Mobile app API expansion (Sanctum tokens)

---

## Appendix A — Seeders

| Seeder | Purpose |
|--------|---------|
| `RolesAndPermissionsSeeder` | Roles, permissions, staff users |
| `PlanSeeder` | Free + paid plans |
| `CvTemplateSeeder` | Template DB records |
| `CmsAndSettingsSeeder` | CMS pages + settings |
| `DatabaseSeeder` | Orchestrates all + test user |

## Appendix B — Artisan Commands

| Command | Schedule | Purpose |
|---------|----------|---------|
| `subscriptions:expire` | Hourly | Expire subscriptions, downgrade to free |
| `queue:prune-batches` | Daily | Clean old batch records |
| `model:prune` | Daily | Prune old models |

## Appendix C — Config Files Reference

| File | Purpose |
|------|---------|
| `config/app.php` | App name, timezone, locale |
| `config/ai.php` | OpenAI settings, prompts, credit costs |
| `config/cv-templates.php` | 10 template definitions + render modes |
| `config/cv.php` | CV builder defaults |
| `config/pdf.php` | PDF export disk, margins, watermark |
| `config/browsershot.php` | Node/Chrome paths, PDF format |
| `config/payfast.php` | PayFast URLs and credentials |
| `config/permissions.php` | Role → permission matrix |
| `config/permission.php` | Spatie permission config |
| `config/marketing.php` | Marketing page content |
| `config/dashboard.php` | Dashboard config |

---

**End of documentation.**  
For questions about a specific file, search this document by module name or route path.
