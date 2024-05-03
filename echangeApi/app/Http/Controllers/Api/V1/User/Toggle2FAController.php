<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Requests\Api\V1\User\Toggle2FARequest;
use App\Models\User;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;
use Illuminate\Support\Facades\Auth;

class Toggle2FAController extends JsonApiController
{
    public function toggle2FA(Toggle2FARequest $request)
    {
        /** @var User */
        $user = Auth::user();

        $user->is_2fa_enabled = ! $user->is_2fa_enabled;
        if ($user->is_2fa_enabled) {
            $user->two_fa_enabled_at = now();
        } else {
            $user->two_fa_disabled_at = now();
        }
        $user->save();

        return $this->reply()->content($user);
    }
}
