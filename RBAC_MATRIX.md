# RBAC MATRIX - Global Consultancy Education CRM

## Overview
**Roles:** 4 (Admin, Manager, Staff, Candidate/Student)  
**Principle:** Never rely only on hiding menu items. Every route must have `auth` + `role/permission` middleware. Every controller action must use `Policies` & `Gates`.  
**Permission Table Structure:** `permissions` -> `role_permissions` -> `users`

## Permission Matrix

| Module | Admin | Manager | Staff | Candidate |
| :--- | :---: | :---: | :---: | :---: |
| **User Management** | CRUD | View Team | - | - |
| **Candidate Management** | Full | Team | Assigned | Own Only |
| **Applications** | Full | Team | Assigned | Own Only View |
| **Universities/Courses** | CRUD | View | View | View |
| **Documents Upload** | All | Team | Assigned | Own |
| **Documents Verify/Reject** | Yes | Yes | No | No |
| **Offers/CAS/Visa/Enrolment** | CRUD | Team | Assigned Edit | View |
| **Commission/Invoice/Payment** | CRUD | View Team | - | - |
| **Website CMS** | CRUD | - | - | - |
| **Settings/Audit Logs** | Full | - | - | - |

## Detailed Permission Breakdown

### 1. User Management
| Permission | Admin | Manager | Staff | Candidate |
| :--- | :---: | :---: | :---: | :---: |
| `users.view` | ✅ | ⚠️ View own team only | ❌ | ❌ |
| `users.create` | ✅ | ❌ | ❌ | ❌ |
| `users.edit` | ✅ | ⚠️ Own profile only | ❌ | ❌ |
| `users.delete` | ✅ | ❌ | ❌ | ❌ |

### 2. Candidate Management
| Permission | Admin | Manager | Staff | Candidate |
| :--- | :---: | :---: | :---: | :---: |
| `candidates.view` | ✅ | ✅ All team candidates | ✅ Assigned candidates only | ✅ Own only |
| `candidates.create` | ✅ | ⚠️ Can create for team | ❌ | ❌ |
| `candidates.edit` | ✅ | ✅ Assigned candidates | ✅ Assigned candidates only | ❌ |
| `candidates.delete` | ✅ | ⚠️ With approval | ❌ | ❌ |
| `candidates.import` | ✅ | ⚠️ Can import team | ❌ | ❌ |
| `candidates.export` | ✅ | ⚠️ Team candidates only | ❌ | ❌ |

### 3. Applications
| Permission | Admin | Manager | Staff | Candidate |
| :--- | :---: | :---: | :---: | :---: |
| `applications.view` | ✅ | ✅ All team applications | ✅ Assigned applications only | ✅ Own only |
| `applications.create` | ✅ | ⚠️ Can create for team | ⚠️ Assigned candidates only | ❌ |
| `applications.edit` | ✅ | ✅ All team applications | ✅ Assigned applications only | ❌ |
| `applications.delete` | ✅ | ❌ | ❌ | ❌ |
| `applications.status_change` | ✅ | ✅ All team | ✅ Assigned only | ❌ |
| `applications.export` | ✅ | ⚠️ Team applications only | ❌ | ❌ |

### 4. Universities & Courses
| Permission | Admin | Manager | Staff | Candidate |
| :--- | :---: | :---: | :---: | :---: |
| `universities.view` | ✅ | ✅ | ✅ | ✅ |
| `universities.create` | ✅ | ❌ | ❌ | ❌ |
| `universities.edit` | ✅ | ⚠️ Own university edits only | ❌ | ❌ |
| `courses.view` | ✅ | ✅ | ✅ | ✅ |
| `courses.create` | ✅ | ⚠️ For assigned university | ❌ | ❌ |
| `courses.edit` | ✅ | ⚠️ For assigned university | ❌ | ❌ |

### 5. Documents
| Permission | Admin | Manager | Staff | Candidate |
| :--- | :---: | :---: | :---: | :---: |
| `documents.upload` | ✅ | ✅ Team | ✅ Assigned candidates | ✅ Own only |
| `documents.view` | ✅ | ✅ All team docs | ✅ Assigned candidates | ✅ Own only |
| `documents.verify` | ✅ | ✅ | ❌ | ❌ |
| `documents.reject` | ✅ | ✅ | ❌ | ❌ |
| `documents.download` | ✅ | ✅ Policy check | ✅ Policy check | ❌ |
| `documents.version` | ✅ | ✅ Version control | ✅ Version control | ✅ Own versions |

### 6. Offers/CAS/Visa/Enrolment
| Permission | Admin | Manager | Staff | Candidate |
| :--- | :---: | :---: | :---: | :---: |
| `offers.view` | ✅ | ✅ Team | ✅ Assigned edit | ✅ View only |
| `offers.create` | ✅ | ⚠️ For assigned | ⚠️ Assigned edit | ❌ |
| `offers.edit` | ✅ | ✅ All team | ✅ Assigned edit | ❌ |
| `cas.view` | ✅ | ✅ Team | ✅ Assigned | ✅ View only |
| `cas.edit` | ✅ | ✅ Team | ✅ Assigned edit | ❌ |
| `visa.view` | ✅ | ✅ Team | ✅ Assigned | ✅ View only |
| `visa.edit` | ✅ | ✅ Team | ✅ Assigned edit | ❌ |
| `enrolment.view` | ✅ | ✅ Team | ✅ Assigned | ✅ View only |
| `enrolment.edit` | ✅ | ✅ Team | ✅ Assigned edit | ❌ |

### 7. Commission/Invoice/Payment
| Permission | Admin | Manager | Staff | Candidate |
| :--- | :---: | :---: | :---: | :---: |
| `commissions.view` | ✅ | ✅ Team | ❌ | ❌ |
| `commissions.claim` | ✅ | ✅ Team | ❌ | ❌ |
| `invoices.view` | ✅ | ✅ Team | ❌ | ❌ |
| `invoices.generate` | ✅ | ❌ | ❌ | ❌ |
| `payments.view` | ✅ | ✅ Team | ❌ | ❌ |
| `payments.record` | ✅ | ❌ | ❌ | ❌ |

### 8. Website CMS
| Permission | Admin | Manager | Staff | Candidate |
| :--- | :---: | :---: | :---: | :---: |
| `cms.edit` | ✅ | ❌ | ❌ | ❌ |
| `cms.view` | ✅ | ❌ | ❌ | ❌ |

### 9. Settings/Audit Logs
| Permission | Admin | Manager | Staff | Candidate |
| :--- | :---: | :---: | :---: | :---: |
| `settings.edit` | ✅ | ❌ | ❌ | ❌ |
| `audit.logs.view` | ✅ | ❌ | ❌ | ❌ |

## Role Mapping & Middleware

### Admin Role
- **Full system access** - All modules, all CRUD operations
- Can manage users, roles, permissions
- Can access all candidates, applications, documents regardless of assignment
- Can view all commissions, invoices, payments
- Can edit settings, view audit logs

### Manager Role
- **Team management** - Can access candidates/applications assigned to their team
- Cannot access candidates outside their assigned team
- Can verify/reject documents for assigned candidates
- Can create/edit offers, CAS, visas for assigned candidates
- Can view but not create commissions/invoices/payments
- Cannot access settings or audit logs

### Staff Role
- **Daily operations** - Can access only their assigned candidates/applications
- Can upload documents for own assignments
- Cannot verify/reject documents
- Can edit offers/CAS/visa/enrolment only for assigned candidates
- Cannot access user management, commissions, invoices, payments
- Cannot access settings or audit logs

### Candidate/Student Role
- **External portal** - Can only access own data
- Can view own candidate profile, applications, documents
- Can upload own documents
- Can view own offers, CAS, visa status, enrolment
- Cannot access any admin/manager/staff features
- Cannot view other candidates' data
- Cannot create/edit anything

## Middleware Implementation

### Route Middleware Chain
Every CRM route must have this middleware chain:
```php
middleware(['auth', 'role:<role>', 'permission:<permission>'])
```

### Example Middleware Assignment

```php
// Admin routes - full access
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('candidates', CandidateController::class);
    Route::resource('applications', ApplicationController::class);
    // ...
});

// Manager routes - team-scoped
Route::middleware(['auth', 'role:manager', 'permission:candidates.view'])->group(function () {
    Route::get('candidates', [CandidateController::class, 'index']);
    // Only candidates assigned to manager's team
});

// Staff routes - assigned only
Route::middleware(['auth', 'role:staff', 'permission:candidates.view'])->group(function () {
    Route::get('candidates', [CandidateController::class, 'index']);
    // Only candidates assigned to authenticated staff member
});

// Candidate routes - own data only
Route::middleware(['auth', 'role:candidate'])->group(function () {
    Route::get('profile', [CandidateController::class, 'profile']);
    Route::get('applications', [ApplicationController::class, 'index']);
    // Only candidate's own data
});
```

### Policy & Gate Usage

#### Example Policy
```php
// app/Policies/CandidatePolicy.php
class CandidatePolicy
{
    public function view(User $user, Candidate $candidate)
    {
        // Admin can view all
        if ($user->role->name === 'admin') {
            return true;
        }
        
        // Manager can view team
        if ($user->role->name === 'manager') {
            return $candidate->assigned_to_user_id === $user->id || 
                   $candidate->team->contains($user);
        }
        
        // Staff can view assigned only
        if ($user->role->name === 'staff') {
            return $candidate->assigned_staff_id === $user->id;
        }
        
        // Candidate can view own
        return $candidate->user_id === $user->id;
    }
    
    public function edit(User $user, Candidate $candidate)
    {
        // Similar logic with additional checks
        return $this->view($user, $candidate) && /* edit-specific checks */;
    }
    
    public function uploadDocument(User $user, Candidate $candidate)
    {
        // Candidate can upload own documents
        // Staff can upload for assigned candidates
        // Admin/Manager can upload for anyone
    }
}
```

#### Example Gate
```php
// app/Providers/AuthServiceProvider.php
public function registerPolicies()
{
    Policy::Model(Candidate::class, CandidatePolicy::class);
    Policy::Model(Application::class, ApplicationPolicy::class);
    Policy::Model(Document::class, DocumentPolicy::class);
    // ...
}

// Usage in controllers
public function show(Candidate $candidate)
{
    abort_if(Gate::allows('candidate-view', $candidate), 403);
    // ...
}
```

## RBAC Implementation Notes

1. **Never trust client-side only** - RBAC must be enforced server-side
2. **Menu items hiding is supplementary** - Primary protection is middleware + policies
3. **All API endpoints** must also check permissions/gates
4. **Permission caching** - Cache permission checks for performance, but allow refresh
5. **Super admin bypass** - Consider a "super_admin" role that bypasses all checks (for system operations)
6. **Permission inheritance** - Managers inherit staff permissions + their team permissions
7. **Audit every permission check** - Log when access is denied for security monitoring