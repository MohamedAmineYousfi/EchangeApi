<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\CodeVerificationRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    public function __invoke(CodeVerificationRequest $request)
    {
        /** @var User */
        $user = Auth::user();
        if ((now() >= $user->verification_code_expires_at)) {
            return response()->json([
                'title' => 'EXPIRED CODE',
                'success' => false,
                'message' => __('notifications.invalid_or_expired_code', []),
                'status' => '400',
            ], 400);
        }

        $token = $user->createToken('2fa-token');
        $token->token->update([
            'is_enable_2fa' => true,
        ]);
        $user->update([
            'two_fa_code' => null,
            'verification_code_expires_at' => null,
        ]);

        return [
            'success' => true,
            'token_type' => 'Bearer',
            'expires_in' => now()->addYear()->timestamp,
            'access_token' => $token->accessToken,
            'message' => __('notifications.connected', []),
        ];
    }
}
