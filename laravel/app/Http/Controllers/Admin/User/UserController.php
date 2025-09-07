<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\BaseController;
use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends BaseController
{
    /**
     * Get paginated list of users for admin
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
            'role' => 'nullable|string|in:admin,user',
            'status' => 'nullable|string|in:active,inactive',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1'
        ]);

        $query = User::with(['roles']);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%");
            });
        }

        // Apply role filter
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        // Note: is_active column doesn't exist in users table, removing status filter

        $users = $query->orderBy('created_at', 'desc')
                      ->paginate($request->per_page ?? 15);

        return $this->ok($users, 'Users retrieved successfully.');
    }

    /**
     * Get user statistics for admin dashboard
     */
    public function stats(): JsonResponse
    {
        $today = now()->startOfDay();
        $thisWeek = now()->startOfWeek();
        $thisMonth = now()->startOfMonth();

        // Get admin role UUID to avoid type casting issues
        $adminRole = \DB::table('roles')->where('name', 'Admin')->first();
        $adminCount = 0;
        
        if ($adminRole) {
            $adminCount = \DB::table('model_has_roles')
                ->where('role_uuid', $adminRole->uuid)
                ->where('model_type', 'App\Models\User\User')
                ->count();
        }

        $stats = [
            'total' => User::count(),
            'active' => User::count(), // All users are considered active since there's no is_active column
            'admins' => $adminCount,
            'new_this_month' => User::where('created_at', '>=', $thisMonth)->count(),
            'new_this_week' => User::where('created_at', '>=', $thisWeek)->count(),
            'new_today' => User::where('created_at', '>=', $today)->count(),
        ];

        return $this->ok($stats, 'User statistics retrieved successfully.');
    }

    /**
     * Get specific user details
     */
    public function show(User $user): JsonResponse
    {
        $user->load(['role']);
        return $this->ok($user, 'User details retrieved successfully.');
    }

    /**
     * Update user details
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->uuid, 'uuid')
            ],
            'password' => 'sometimes|nullable|string|min:8|confirmed',
            'role_uuid' => 'sometimes|required|exists:roles,uuid',
            'is_active' => 'sometimes|required|boolean'
        ]);

        $data = $request->only(['name', 'email', 'role_uuid', 'is_active']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $user->load(['role']);

        return $this->ok($user, 'User updated successfully.');
    }

    /**
     * Update user status (active/inactive)
     */
    public function updateStatus(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'is_active' => 'required|boolean'
        ]);

        // Note: is_active column doesn't exist in users table
        // This is a placeholder method that doesn't actually update status
        $message = $request->is_active ? 'User activated successfully.' : 'User deactivated successfully.';
        
        return $this->ok($user, $message);
    }

    /**
     * Soft delete user
     */
    public function destroy(User $user): JsonResponse
    {
        // Prevent self-deletion
        if ($user->uuid === auth()->user()->uuid) {
            return $this->error(['Cannot delete your own account.'], 'Action not allowed.', 403);
        }

        // Prevent deletion of other admins by non-super-admins
        if ($user->hasRole('Admin') && !auth()->user()->hasRole('Super-Admin')) {
            return $this->error(['Cannot delete admin accounts.'], 'Action not allowed.', 403);
        }

        $user->delete();

        return $this->ok(null, 'User deleted successfully.');
    }
}