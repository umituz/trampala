<?php

namespace App\Enums\User;

/**
 * User type enumeration for role-based access control in trampala
 *
 * Defines the available user roles in the language learning platform
 * and their associated permissions. This enum is used throughout the
 * application to enforce role-based access control.
 *
 * @method static self ADMIN() Administrator role with full system access
 * @method static self USER() Regular user role for platform users
 */
enum UserTypeEnum: string
{
    /**
     * Administrator role
     * Has full access to all system features including user management,
     * matching system, course management, and system configuration.
     */
    case ADMIN = 'admin';

    /**
     * User role
     * Regular platform users with standard access to learning features,
     * lessons, scheduling, and personal progress tracking.
     */
    case USER = 'user';

    /**
     * Get all available user type values
     *
     * Returns an array of string values for all user types.
     * Useful for validation rules and select options.
     *
     * @return array Array of user type values ['admin', 'user']
     */
    public static function getAvailableTypes(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get human-readable label for the user type
     *
     * Provides display-friendly names for UI presentation.
     *
     * @return string Formatted label for the user type
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator',
            self::USER => 'User',
        };
    }

    /**
     * Get default email address for the user type
     *
     * Returns predefined email addresses used for testing and seeding.
     * These are the default emails used in the authentication system.
     *
     * @return string Default email address for the user type
     */
    public function getEmail(): string
    {
        return match ($this) {
            self::ADMIN => 'admin@test.com',
            self::USER => 'user@test.com',
        };
    }

    /**
     * Check if user type has administrative access
     *
     * Determines whether this user type has access to admin-only features.
     *
     * @return bool True if user has admin access, false otherwise
     */
    public function hasAdminAccess(): bool
    {
        return $this === self::ADMIN;
    }

    /**
     * Check if user type can manage lessons
     *
     * Determines whether this user type can create and manage lessons.
     *
     * @return bool True if user can manage lessons
     */
    public function canManageLessons(): bool
    {
        return $this === self::ADMIN;
    }

    /**
     * Check if user type can manage matching
     *
     * Determines whether this user type can manage user matching.
     *
     * @return bool True if user can manage matching
     */
    public function canManageMatching(): bool
    {
        return $this === self::ADMIN;
    }

    /**
     * Check if user is a regular user
     *
     * @return bool True if user is a regular user
     */
    public function isUser(): bool
    {
        return $this === self::USER;
    }

    /**
     * Check if user is an admin
     *
     * @return bool True if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    /**
     * Check if user type can manage users
     *
     * Determines whether this user type can view, create, update,
     * and manage other users in the system.
     *
     * @return bool True if user can manage other users
     */
    public function canManageUsers(): bool
    {
        return $this === self::ADMIN;
    }
}
