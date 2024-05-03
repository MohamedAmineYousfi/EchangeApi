<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Laravel\Passport\Token;

class TwoFactorTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        $accessToken = $request->bearerToken();

        if (! $accessToken) {
            abort(401, __('notifications.no_token_provided', []));
        }

        $tokenParts = explode('.', $accessToken);
        if (count($tokenParts) !== 3) {
            abort(401, __('notifications.invalid_token_structure', []));
        }

        $payload = base64_decode($tokenParts[1]);
        $tokenData = json_decode($payload, true);

        if (! isset($tokenData['jti'])) {
            abort(401, __('notifications.please_provide_a_token', []));
        }

        $token = Token::find($tokenData['jti']);

        if (! $token) {
            abort(401, __('notifications.invalid_token', []));
        }

        $user = auth()->user();
        if (! $user->is_2fa_enabled || $token['is_enable_2fa']) {
            return $next($request);
        }

        abort(403, __('notifications.two_factor_authentication_required', []));
    }
}
