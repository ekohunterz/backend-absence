<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Major;
use Illuminate\Auth\Access\HandlesAuthorization;

class MajorPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Major');
    }

    public function view(AuthUser $authUser, Major $major): bool
    {
        return $authUser->can('View:Major');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Major');
    }

    public function update(AuthUser $authUser, Major $major): bool
    {
        return $authUser->can('Update:Major');
    }

    public function delete(AuthUser $authUser, Major $major): bool
    {
        return $authUser->can('Delete:Major');
    }

}