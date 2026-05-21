# Response System Usage Guide

## Overview
The Response System provides a standardized way to send JSON responses across your application. All responses follow the structure: `{ status, message, data }`.

## Response Structure
```json
{
  "status": "success|error",
  "message": "Response message",
  "data": null
}
```

## Usage Examples

### 1. Success Response
**Using the static method:**
```php
use App\Services\ResponseService;

return ResponseService::success('User created successfully', $user, 201);
```

**Using the helper function:**
```php
return response_success('User created successfully', $user, 201);
```

### 2. Error Response
**Using the static method:**
```php
return ResponseService::error('Something went wrong', null, 400);
```

**Using the helper function:**
```php
return response_error('Something went wrong', null, 400);
```

### 3. Not Found (404)
```php
return response_not_found('User not found');
```

Response:
```json
{
  "status": "error",
  "message": "User not found",
  "data": null
}
```

### 4. Unauthorized (401)
```php
return response_unauthorized('Please login first');
```

### 5. Forbidden (403)
```php
return response_forbidden('You do not have permission to access this resource');
```

### 6. Validation Error (422)
```php
return response_validation_error([
    'email' => ['The email must be a valid email address'],
    'password' => ['The password must be at least 8 characters']
], 'Please fix the validation errors');
```

Response:
```json
{
  "status": "error",
  "message": "Please fix the validation errors",
  "data": {
    "errors": {
      "email": ["The email must be a valid email address"],
      "password": ["The password must be at least 8 characters"]
    }
  }
}
```

### 7. Server Error (500)
```php
return response_server_error('An unexpected error occurred');
```

## Controller Example

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response_success('Users fetched successfully', $users);
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response_not_found('User not found');
        }

        return response_success('User fetched successfully', $user);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
        ]);

        if ($validated === false) {
            return response_validation_error($validator->errors()->toArray());
        }

        $user = User::create($validated);
        return response_success('User created successfully', $user, 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response_not_found('User not found');
        }

        $user->update($request->all());
        return response_success('User updated successfully', $user);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response_not_found('User not found');
        }

        $user->delete();
        return response_success('User deleted successfully');
    }

    public function fixData($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response_not_found('User not found');
        }

        if (!$user->is_active) {
            return response_requires_fix('User account is inactive and requires fixing');
        }

        try {
            // Perform fix operation
            $user->repair_data();
            return response_fix_applied('User data repaired successfully', $user);
        } catch (\Exception $e) {
            return response_fix_failed('Failed to repair user data: ' . $e->getMessage());
        }
    }
}
```

## Fix Methods Example

```php
// Fix operation that succeeded
public function repairDatabase()
{
    try {
        DB::statement('REPAIR TABLE users');
        DB::statement('REPAIR TABLE orders');
        return response_fix_applied('Database tables repaired', ['tables' => 2]);
    } catch (\Exception $e) {
        return response_fix_failed('Database repair failed', ['error' => $e->getMessage()]);
    }
}

// Check if item needs fixing
public function validateOrder($orderId)
{
    $order = Order::find($orderId);

    if (!$order) {
        return response_not_found('Order not found');
    }

    if ($order->has_errors) {
        return response_requires_fix('Order has data inconsistencies', ['error_count' => 3]);
    }

    return response_success('Order is valid', $order);
}
```

## Fix Methods

### 8. Fix Applied (200)
```php
return response_fix_applied('Database repaired successfully', ['tables_fixed' => 5]);
```

Response:
```json
{
  "status": "fixed",
  "message": "Database repaired successfully",
  "data": {"tables_fixed": 5}
}
```

### 9. Requires Fix (400)
```php
return response_requires_fix('Image format needs conversion', ['current_format' => 'bmp']);
```

Response:
```json
{
  "status": "requires_fix",
  "message": "Image format needs conversion",
  "data": {"current_format": "bmp"}
}
```

### 10. Fix Failed (400)
```php
return response_fix_failed('Could not repair corrupted file', ['reason' => 'File integrity check failed']);
```

## Available Methods

| Method | HTTP Code | Status | Usage |
|--------|-----------|--------|-------|
| `response_success()` | 200 | success | For successful operations |
| `response_error()` | 400 | error | For general errors |
| `response_not_found()` | 404 | error | When resource is not found |
| `response_unauthorized()` | 401 | error | When user is not authenticated |
| `response_forbidden()` | 403 | error | When user lacks permissions |
| `response_validation_error()` | 422 | error | For validation failures |
| `response_server_error()` | 500 | error | For server errors |
| `response_fix_applied()` | 200 | fixed | When fix is successfully applied |
| `response_requires_fix()` | 400 | requires_fix | When item requires fixing |
| `response_fix_failed()` | 400 | error | When fix operation fails |

## Notes
- Helper functions are automatically loaded via `AppServiceProvider`
- All methods return `Illuminate\Http\JsonResponse` instances
- Default status code is 200 for success and 400 for errors
- You can override status codes when needed
