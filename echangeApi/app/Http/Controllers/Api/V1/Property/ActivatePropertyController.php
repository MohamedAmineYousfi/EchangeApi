<?php

namespace App\Http\Controllers\Api\V1\Property;

use App\Constants\Permissions;
use App\Http\Requests\Api\V1\Property\ActivatePropertyRequest;
use App\Models\Property;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;

class ActivatePropertyController extends JsonApiController
{
    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function activate(ActivatePropertyRequest $request, Property $property)
    {
        if (! auth()->user()->can(Permissions::PERM_TOGGLE_ACTIVATION_PROPERTIES)) {
            abort(403, __('notifications.unauthorized_activate_deactivate_properties', []));
        }

        $property->active = true;
        $property->save();

        return $this->reply()->content($property);
    }
}
