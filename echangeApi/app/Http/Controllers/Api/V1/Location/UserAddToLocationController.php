<?php

namespace App\Http\Controllers\Api\V1\Location;

use App\Http\Requests\Api\V1\Location\UserAddToLocationFormRequest;
use App\Models\Location;
use App\Models\User;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserAddToLocationController extends JsonApiController
{
    /**
     * Handle the incoming request.
     */
    public function attachUserToLocation(UserAddToLocationFormRequest $request, Location $location): Response
    {
        $data = [];
        foreach ($request->users as $userId) {
            $data[$userId] = ['model_type' => User::class];
        }
        $location->users()->syncWithoutDetaching($data);

        return $this->reply()->content($location);
    }

    /**
     * Handle the incoming request.
     */
    public function detachUserToLocation(UserAddToLocationFormRequest $request, Location $location): Response
    {
        foreach ($request->users as $userId) {
            $location->users()->detach($userId);
        }

        return $this->reply()->content($location);
    }
}
