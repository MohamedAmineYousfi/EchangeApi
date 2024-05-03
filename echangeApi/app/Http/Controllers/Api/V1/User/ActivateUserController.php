<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Requests\Api\V1\User\ActivateUserRequest;
use App\Mail\User\ActivateUser;
use App\Models\User;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ActivateUserController extends JsonApiController
{
    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function activate(ActivateUserRequest $request, User $user)
    {
        $user->active = true;
        $user->save();

        $mail = new ActivateUser($user);
        Mail::to($user->email)->send($mail);

        return $this->reply()->content($user);
    }
}
