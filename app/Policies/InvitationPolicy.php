<?php

namespace App\Policies;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvitationPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function resend(User $user,Invitation $invitation)
    {
        return $user->id==$invitation->sender_id;
    }

    public function respond(User $user,Invitation $invitation)
    {
        return $user->email==$invitation->recipient_email;
    }

    public function delete(User $user,Invitation $invitation)
    {
        return $user->id==$invitation->sender_id;
    }
}
