# FamilyAid System - Technical Documentation

> **Purpose**: This document provides a comprehensive description of the FamilyAid System for AI agents and developers to understand the architecture, features, and implementation details.

---

## 1. System Overview

**FamilyAid** is a **humanitarian aid management system** built with **Laravel 11** for managing household registrations, aid program distributions, and tracking benefits for families in need.

### Core Purpose
- Register and verify households (families) eligible for humanitarian aid
- Manage multiple aid programs (food, cash, medical supplies, etc.)
- Track and record aid distributions to households
- Generate reports and exports for accountability
- Support both Arabic (RTL) and English languages

### Technology Stack
| Component | Technology |
|-----------|------------|
| Framework | Laravel 11 |
| PHP Version | 8.2+ |
| Database | MySQL/MariaDB |
| Frontend | Blade + Tailwind CSS + Alpine.js |
| Authentication | Laravel Breeze (customized) |
| Authorization | Spatie Laravel Permission |
| Excel Import/Export | Maatwebsite Excel |
| Local Server | XAMPP |
| Path | `c:\xampp\htdocs\FamilyAid System\` |

---

## 2. Database Schema

### Core Tables

```
┌─────────────────────────────────────────────────────────────────────┐
│                           DATABASE SCHEMA                            │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  ┌──────────────┐         ┌─────────────────────┐                   │
│  │   regions    │         │       users         │                   │
│  ├──────────────┤         ├─────────────────────┤                   │
│  │ id           │◄────┐   │ id                  │                   │
│  │ parent_id    │     │   │ national_id (unique)│                   │
│  │ name         │     │   │ name                │                   │
│  │ code         │     │   │ phone               │                   │
│  └──────────────┘     │   │ household_id (FK)   │──┐                │
│                       │   │ security_question   │  │                │
│  ┌──────────────────┐ │   │ security_answer     │  │                │
│  │   households     │◄┘   └─────────────────────┘  │                │
│  ├──────────────────┤                              │                │
│  │ id              ◄───────────────────────────────┘                │
│  │ head_national_id │                                               │
│  │ head_name        │         ┌──────────────────────┐              │
│  │ region_id (FK)   │         │  household_members   │              │
│  │ address_text     │         ├──────────────────────┤              │
│  │ housing_type     │◄────────│ household_id (FK)    │              │
│  │ primary_phone    │         │ national_id          │              │
│  │ secondary_phone  │         │ full_name            │              │
│  │ status           │         │ relation_to_head     │              │
│  │ notes            │         │ gender               │              │
│  │ has_war_injury   │         │ birth_date           │              │
│  │ has_chronic_disease│       │ notes                │              │
│  │ has_disability   │         └──────────────────────┘              │
│  │ condition_notes  │                                               │
│  └──────────────────┘                                               │
│           │                                                          │
│           │          ┌───────────────────┐                          │
│           │          │   aid_programs    │                          │
│           │          ├───────────────────┤                          │
│           │          │ id                │                          │
│           │          │ name              │                          │
│           │          │ description       │                          │
│           │          │ start_date        │                          │
│           │          │ end_date          │                          │
│           │          │ is_active         │                          │
│           │          │ allow_multiple    │                          │
│           │          └───────────────────┘                          │
│           │                    │                                     │
│           │                    ▼                                     │
│           │         ┌────────────────────┐                          │
│           └────────►│   distributions    │◄─────────────────────────┤
│                     ├────────────────────┤                          │
│                     │ household_id (FK)  │                          │
│                     │ aid_program_id (FK)│                          │
│                     │ distribution_date  │                          │
│                     │ recorded_by (FK)   │                          │
│                     │ notes              │                          │
│                     └────────────────────┘                          │
│                                                                      │
│  ┌─────────────────┐        ┌──────────────────┐                    │
│  │   audit_logs    │        │   import_jobs    │                    │
│  ├─────────────────┤        ├──────────────────┤                    │
│  │ action          │        │ file_name        │                    │
│  │ model_type      │        │ type             │                    │
│  │ model_id        │        │ status           │                    │
│  │ before/after    │        │ rows_processed   │                    │
│  │ user_id         │        │ rows_failed      │                    │
│  │ ip_address      │        │ errors           │                    │
│  └─────────────────┘        │ uploaded_by      │                    │
│                             └──────────────────┘                    │
└─────────────────────────────────────────────────────────────────────┘
```

### Field Details

#### `households` Table
| Column | Type | Description |
|--------|------|-------------|
| `head_national_id` | VARCHAR(20) | Unique national ID of household head |
| `head_name` | VARCHAR(255) | Full name of household head |
| `region_id` | FK → regions | Geographic region |
| `address_text` | TEXT | Full address details |
| `housing_type` | ENUM | `owned`, `rented`, `family_hosted`, `other` |
| `primary_phone` | VARCHAR(20) | Primary contact number |
| `secondary_phone` | VARCHAR(20) | Alternate contact |
| `status` | ENUM | `pending`, `verified`, `suspended`, `rejected` |
| `has_war_injury` | BOOLEAN | Household has war injury victim |
| `has_chronic_disease` | BOOLEAN | Household has chronic disease patient |
| `has_disability` | BOOLEAN | Household has disabled member |
| `condition_notes` | TEXT | Additional health notes |

#### `regions` Table (Hierarchical)
- Parent regions (e.g., "North Gaza")
- Child regions (e.g., "Beit Lahia", "Jabalia")
- Self-referential `parent_id` for nesting

---

## 3. User Roles & Permissions

The system uses **Spatie Laravel Permission** for role-based access control.

### Roles

| Role | Description | Access Level |
|------|-------------|--------------|
| `admin` | Full system administrator | All permissions |
| `data_entry` | Data entry staff | CRUD households, record distributions |
| `auditor` | Auditors/supervisors | Read-only + exports + audit logs |
| `distributor` | Field distributors | View households, record distributions |
| `citizen` | Beneficiary family head | Manage own household only |

### Permission Categories
- **Households**: `view`, `create`, `update`, `delete`, `verify`, `import`, `export`
- **Members**: `view`, `create`, `update`, `delete`
- **Programs**: `view`, `create`, `update`, `delete`
- **Distributions**: `view`, `create`, `update`, `delete`, `export`
- **Reports**: `view`, `export`
- **Audit Logs**: `view`
- **Own Household**: `view`, `update` (citizens only)

---

## 4. Routes & Controllers

### Authentication Routes (`routes/auth.php`)
| Route | Controller | Description |
|-------|------------|-------------|
| `GET /login` | `AuthenticatedSessionController@create` | Login page |
| `POST /login` | `AuthenticatedSessionController@store` | Process login |
| `GET /register` | `RegisteredUserController@create` | Registration page |
| `POST /register` | `RegisteredUserController@store` | Process registration |
| `GET /forgot-password` | `SecurityQuestionController@showForm` | Password reset via security question |
| `POST /forgot-password/verify-id` | `SecurityQuestionController@verifyNationalId` | Verify national ID |
| `POST /forgot-password/verify-answer` | `SecurityQuestionController@verifyAnswer` | Verify security answer |
| `POST /forgot-password/reset` | `SecurityQuestionController@resetPassword` | Reset password |
| `POST /logout` | `AuthenticatedSessionController@destroy` | Logout |

### Citizen Routes (`/citizen/*`)
Protected by `role:citizen` middleware.

| Route | Controller | Description |
|-------|------------|-------------|
| `GET /citizen/onboarding` | `OnboardingController@index` | Household registration wizard |
| `POST /citizen/onboarding` | `OnboardingController@store` | Submit household registration |
| `GET /citizen/dashboard` | `DashboardController@index` | Citizen dashboard |
| `GET /citizen/household/edit` | `DashboardController@edit` | Edit household form |
| `PUT /citizen/household` | `DashboardController@update` | Update household |
| `GET /citizen/members` | `MemberController@index` | Manage household members |
| `POST /citizen/members` | `MemberController@store` | Add member |
| `PUT /citizen/members/{id}` | `MemberController@update` | Update member |
| `DELETE /citizen/members/{id}` | `MemberController@destroy` | Remove member |

### Admin Routes (`/admin/*`)
Protected by `role:admin|data_entry|auditor|distributor` middleware.

| Route | Controller | Description |
|-------|------------|-------------|
| `GET /admin/dashboard` | `AdminDashboardController@index` | Admin dashboard with stats |
| **Households** | | |
| `GET /admin/households` | `HouseholdController@index` | List all households (with filters) |
| `GET /admin/households/create` | `HouseholdController@create` | Create household form |
| `POST /admin/households` | `HouseholdController@store` | Store new household |
| `GET /admin/households/{id}` | `HouseholdController@show` | View household details |
| `GET /admin/households/{id}/edit` | `HouseholdController@edit` | Edit household form |
| `PUT /admin/households/{id}` | `HouseholdController@update` | Update household |
| `DELETE /admin/households/{id}` | `HouseholdController@destroy` | Delete household |
| `POST /admin/households/{id}/verify` | `HouseholdController@verify` | Verify pending household |
| **Programs** | | |
| `GET /admin/programs` | `AidProgramController@index` | List aid programs |
| `GET /admin/programs/create` | `AidProgramController@create` | Create program form |
| `POST /admin/programs` | `AidProgramController@store` | Store program |
| `GET /admin/programs/{id}/edit` | `AidProgramController@edit` | Edit program form |
| `PUT /admin/programs/{id}` | `AidProgramController@update` | Update program |
| `DELETE /admin/programs/{id}` | `AidProgramController@destroy` | Delete program |
| **Distributions** | | |
| `GET /admin/distributions` | `DistributionController@index` | List distributions |
| `GET /admin/distributions/create` | `DistributionController@create` | Record distribution form |
| `POST /admin/distributions` | `DistributionController@store` | Store distribution |
| `GET /admin/distributions/search-household` | `DistributionController@searchHousehold` | AJAX household search |
| `GET /admin/distributions/check-eligibility` | `DistributionController@checkEligibility` | AJAX eligibility check |
| `DELETE /admin/distributions/{id}` | `DistributionController@destroy` | Delete distribution |
| **Import/Export** | | |
| `GET /admin/import-export` | `ImportExportController@index` | Import/Export page |
| `GET /admin/import-export/template` | `ImportExportController@downloadTemplate` | Download CSV template |
| `POST /admin/import-export/import` | `ImportExportController@import` | Import households Excel |
| `GET /admin/import-export/export-households` | `ImportExportController@exportHouseholds` | Export households Excel |
| `GET /admin/import-export/export-distributions` | `ImportExportController@exportDistributions` | Export distributions Excel |
| **Audit Logs** | | |
| `GET /admin/audit-logs` | `AuditLogController@index` | View audit logs |
| `GET /admin/audit-logs/{id}` | `AuditLogController@show` | View log details |

---

## 5. Models & Relationships

### Household Model
```php
// Relationships
- belongsTo(Region::class)        // Geographic location
- hasOne(User::class)             // Citizen account
- hasMany(HouseholdMember::class) // Family members
- hasMany(Distribution::class)    // Aid received

// Scopes (for filtering)
- scopeVerified($query)
- scopePending($query)
- scopeSearchByPhone($query, $phone)
- scopeHasWarInjury($query)
- scopeHasChronicDisease($query)
- scopeHasDisability($query)
- scopeHasChildUnderMonths($query, $months = 24)
```

### User Model
```php
// Authentication via national_id (not email)
- Uses Spatie HasRoles trait
- belongsTo(Household::class)  // For citizens

// Security question for password reset
- security_question: ENUM of predefined questions
- security_answer: hashed answer
```

### Region Model
```php
// Self-referential hierarchy
- belongsTo(Region::class, 'parent_id')  // Parent region
- hasMany(Region::class, 'parent_id')    // Child regions
- hasMany(Household::class)
```

### AidProgram Model
```php
- hasMany(Distribution::class)
- is_active: boolean (can receive distributions)
- allow_multiple: boolean (allow multiple distributions per household)
```

### Distribution Model
```php
- belongsTo(Household::class)
- belongsTo(AidProgram::class)
- belongsTo(User::class, 'recorded_by')  // Who recorded it
```

---

## 6. Key Features

### 6.1 Arabic Localization (RTL)
- Default locale: `ar` (Arabic)
- RTL layout support in all views
- Language files: `resources/lang/ar/` and `resources/lang/en/`
- Language switcher in navigation
- Tajawal Arabic font

### 6.2 Authentication
- Login via **National ID** (not email)
- Password reset via **Security Question** (no email required)
- Two-step registration: 1) Account details, 2) Security question
- Citizens auto-linked to their household

### 6.3 Household Filters (Admin)
- Search by name, national ID, phone
- Filter by status (pending, verified, suspended, rejected)
- Filter by region
- Filter by housing type
- **Health condition filters**: War injury, Chronic disease, Disability
- **Smart filter**: Has child under 2 years (uses birth_date calculation)

### 6.4 Import/Export
- **Import template columns**: national_id, head_name, region, address, housing_type, phone, war_injury, chronic_disease, disability, condition_notes, member_names, member_relations
- **Export includes**: All household fields + health conditions + members
- Supports Excel (.xlsx, .xls) and CSV
- Boolean normalization: Accepts 0/1, yes/no, true/false, نعم/لا

### 6.5 Audit Logging
- Tracks all CRUD operations
- Records: action, model_type, model_id, before/after data, user, IP
- Viewable in admin panel

### 6.6 Distribution Recording
- Select household (AJAX search)
- Select active aid program
- Eligibility check (prevents duplicates if program doesn't allow multiple)
- Records who distributed and when

---

## 7. File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── DashboardController.php
│   │   │   ├── HouseholdController.php
│   │   │   ├── AidProgramController.php
│   │   │   ├── DistributionController.php
│   │   │   ├── ImportExportController.php
│   │   │   └── AuditLogController.php
│   │   ├── Citizen/
│   │   │   ├── DashboardController.php
│   │   │   ├── OnboardingController.php
│   │   │   └── MemberController.php
│   │   ├── Auth/
│   │   │   ├── AuthenticatedSessionController.php
│   │   │   ├── RegisteredUserController.php
│   │   │   └── SecurityQuestionController.php
│   │   └── LanguageController.php
│   └── Middleware/
│       └── SetLocale.php
├── Models/
│   ├── User.php
│   ├── Household.php
│   ├── HouseholdMember.php
│   ├── Region.php
│   ├── AidProgram.php
│   ├── Distribution.php
│   ├── AuditLog.php
│   └── ImportJob.php
├── Exports/
│   ├── HouseholdsExport.php
│   └── DistributionsExport.php
└── Imports/
    └── HouseholdsImport.php

resources/
├── views/
│   ├── layouts/
│   │   ├── app.blade.php
│   │   ├── guest.blade.php
│   │   └── navigation.blade.php
│   ├── auth/
│   │   ├── login.blade.php
│   │   ├── register.blade.php
│   │   ├── forgot-password-security.blade.php
│   │   ├── answer-security-question.blade.php
│   │   └── reset-password-security.blade.php
│   ├── citizen/
│   │   ├── dashboard.blade.php
│   │   ├── onboarding.blade.php
│   │   ├── household-edit.blade.php
│   │   └── members.blade.php
│   └── admin/
│       ├── dashboard.blade.php
│       ├── households/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   ├── show.blade.php
│       │   └── edit.blade.php
│       ├── programs/
│       ├── distributions/
│       └── import-export/
└── lang/
    ├── ar/
    │   ├── auth.php
    │   ├── messages.php
    │   ├── validation.php
    │   └── pagination.php
    └── en/
        ├── auth.php
        └── messages.php
```

---

## 8. Translation Keys Structure

### messages.php Categories
- `nav.*` - Navigation labels
- `actions.*` - Button labels (save, cancel, edit, etc.)
- `status.*` - Status badges (pending, verified, etc.)
- `success.*` / `error.*` - Flash messages
- `household.*` - Household field labels
- `housing_types.*` - Housing type options
- `relations.*` - Family relation options
- `health.*` - Health condition labels
- `child_filter.*` - Child age filter labels
- `exports.households.*` / `exports.distributions.*` - Export headers
- `onboarding_form.*` - Registration wizard labels
- `citizen.*` - Citizen dashboard labels
- `households_admin.*` - Admin household management
- `programs.*` / `distributions.*` - Program/distribution management

---

## 9. Common Development Tasks

### Adding a New Field to Households
1. Create migration: `php artisan make:migration add_field_to_households`
2. Add to `Household` model `$fillable` and `$casts`
3. Add to admin create/edit forms
4. Add translations in `ar/messages.php` and `en/messages.php`
5. Add to `HouseholdsExport` (headings + map)
6. Add to `HouseholdsImport` if importable
7. Update import template in `ImportExportController@downloadTemplate`

### Adding a New Filter
1. Add filter input/checkbox to `admin/households/index.blade.php`
2. Add scope to `Household` model
3. Apply scope in `HouseholdController@index`
4. Add filter key to `$filters` return array
5. Add to `HouseholdsExport@query` to respect filters in exports

### Adding a New Role
1. Add role in `RolesAndPermissionsSeeder`
2. Assign permissions
3. Run `php artisan db:seed --class=RolesAndPermissionsSeeder`
4. Add role check in routes middleware if needed

---

## 10. Environment Configuration

Key `.env` settings:
```env
APP_NAME=FamilyAid
APP_LOCALE=ar
APP_FALLBACK_LOCALE=en

DB_DATABASE=familyaid
DB_USERNAME=root
DB_PASSWORD=
```

---

## 11. Running Commands

```bash
# Serve locally
php artisan serve

# Run migrations
php artisan migrate

# Seed demo data
php artisan db:seed

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Build assets
npm run build
```

---

*Last updated: January 2026*
