<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Requests\Api\V1\User\DeactivateUserRequest;
use App\Mail\User\DeactivateUser;
use App\Models\User;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;
use Illuminate\Support\Facades\Mail;

class DeactivateUserController extends JsonApiController
{
    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function deactivate(DeactivateUserRequest $request, User $user)
    {
        $user->active = false;
        $user->save();

        $mail = new DeactivateUser($user);
        Mail::to($user->email)->send($mail);

        return $this->reply()->content($user);
    }
}
