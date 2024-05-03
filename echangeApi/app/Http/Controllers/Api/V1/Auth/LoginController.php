<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Mail\TwoFactorAuth\AuthenticationCode;
use App\Models\User;
use CloudCreativity\LaravelJsonApi\Document\Error\Error;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;
use Laravel\Passport\Exceptions\OAuthServerException;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends JsonApiController
{
    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function __invoke(LoginRequest $request)
    {
        try {
            app()->setLocale($request->header('locale'));

            $client = DB::table('oauth_clients')->where('password_client', 1)->first();
            $loginReq = Request::create(route('passport.token', [], false), 'POST', [
                'grant_type' => 'password',
                'client_id' => $client->id,
                'client_secret' => $client->secret,
                'username' => $request->email,
                'password' => $request->password,
                'scope' => '',
            ]);
            /** @var Response */
            $response = app()->handle($loginReq);

            $user = User::where('email', '=', $request->email)->first();

            if (! $user) {
                return $this->reply()->errors([
                    Error::fromArray([
                        'title' => 'USER_NOT_FOUND',
                        'detail' => __('errors.invalid_credentials', []),
                        'status' => '400',
                    ]),
                ]);
            }

            if (! $user->active) {
                return $this->reply()->errors([
                    Error::fromArray([
                        'title' => 'INACTIVE_USER',
                        'detail' => __('errors.the_user_is_not_active', []),
                        'status' => '400',
                    ]),
                ]);
            }

            if ($response->getStatusCode() != 200) {
                return $this->reply()->errors([
                    Error::fromArray([
                        'title' => 'Bad Request',
                        'detail' => json_decode((string) $response->getContent(), true)['message'],
                        'status' => '400',
                    ]),
                ]);
            }

            if ($user->is_2fa_enabled) {
                $code = str_pad(strval(random_int(100000, 999999)), 6, '0', STR_PAD_LEFT);
                $user->update([
                    'two_fa_code' => $code,
                    'verification_code_expires_at' => now()->addMinutes(15),
                ]);
                $mail = new AuthenticationCode($user, $code);
                Mail::to($user->email)->send($mail);
                $response = json_decode((string) $response->getContent(), true);

                return response()->json([
                    'success' => true,
                    'access_token' => $response['access_token'] ?? '',
                    'message' => __('notifications.verification_code_sent_success', []),
                    'data' => [
                        'user_id' => $user->id,
                        'verification_code_expires_at' => $user->verification_code_expires_at,
                    ],
                ]);
            }

            return json_decode((string) $response->getContent(), true);
        } catch (Exception|OAuthServerException $e) {
            return $this->reply()->errors([
                Error::fromArray([
                    'title' => 'Bad Request',
                    'detail' => $e->getMessage(),
                    'status' => '500',
                ]),
            ]);
        }
    }
}
