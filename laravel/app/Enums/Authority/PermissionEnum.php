<?php

namespace App\Enums\Authority;

/**
 * Permission enumeration for role-based access control in trampala
 *
 * Defines all available permissions in the language learning platform for users,
 * lessons, teachers, students, matching, and system management. Used for implementing
 * role-based access control (RBAC) throughout the application.
 *
 * @package App\Enums\Authority
 */
enum PermissionEnum: string
{
    // User Management
    case USER_READ = 'users.read';
    case USER_CREATE = 'users.create';
    case USER_UPDATE = 'users.update';
    case USER_DELETE = 'users.delete';

    // Profile Management
    case PROFILE_READ = 'profile.read';
    case PROFILE_UPDATE = 'profile.update';


    // System Management
    case SYSTEM_MANAGE = 'system.manage';

    /**
     * Get human-readable description for the permission
     *
     * @return string Permission description for display purposes
     */
    public function description(): string
    {
        return match($this) {
            self::USER_READ => 'View Users',
            self::USER_CREATE => 'Create Users',
            self::USER_UPDATE => 'Update Users',
            self::USER_DELETE => 'Delete Users',

            self::PROFILE_READ => 'View Profile',
            self::PROFILE_UPDATE => 'Update Profile',

            self::SYSTEM_MANAGE => 'Manage System Settings',
        };
    }

    /**
     * Get all permission values as array
     *
     * @return array Array of all permission string values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all admin permissions
     *
     * Returns all permissions available to admin users including
     * full system management and all CRUD operations.
     *
     * @return array Array of admin permission values
     */
    public static function getAdminPermissions(): array
    {
        return [
            self::USER_READ->value,
            self::USER_CREATE->value,
            self::USER_UPDATE->value,
            self::USER_DELETE->value,
            self::PROFILE_READ->value,
            self::PROFILE_UPDATE->value,
            self::SYSTEM_MANAGE->value,
        ];
    }

    /**
     * Get user permissions
     *
     * Returns permissions for regular users including basic profile management.
     *
     * @return array Array of user permission values
     */
    public static function getUserPermissions(): array
    {
        return [
            self::PROFILE_READ->value,
            self::PROFILE_UPDATE->value,
        ];
    }
}
