<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request)
    {
        auth()->user()->token()->revoke();

        return response()->json([], 204);
    }
}
