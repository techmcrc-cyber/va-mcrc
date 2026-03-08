<?php

namespace App\Helpers;

use App\Models\Organization;

class OrganizationHelper
{
    /**
     * Get the current organization from the request context.
     */
    public static function current(): ?Organization
    {
        return config('app.current_organization');
    }

    /**
     * Get the current organization ID.
     */
    public static function currentId(): ?int
    {
        return self::current()?->id;
    }

    /**
     * Check if we're in an organization context.
     */
    public static function hasContext(): bool
    {
        return self::current() !== null;
    }

    /**
     * Get organization ID for the current user.
     * Returns user's organization_id or current organization from context.
     */
    public static function getUserOrganizationId(): ?int
    {
        $user = auth()->user();
        
        // Super admin can work with any organization
        if ($user && $user->isSuperAdmin()) {
            return self::currentId() ?? request()->get('organization_id');
        }
        
        // Regular users work with their own organization
        return $user?->organization_id;
    }
}
