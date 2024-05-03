<?php

namespace App\Http\Controllers\Api\V1\Notification;

use App\Http\Requests\Api\V1\Notification\MarkAsReadRequest;
use App\Models\Notification;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;

class MarkAsReadController extends JsonApiController
{
    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function markAsRead(MarkAsReadRequest $request)
    {
        $ids = $request->input();
        foreach ($ids as $id) {
            $notification = Notification::find($id);
            $notification->markAsRead();
            $notification->save();
        }

        return $this->reply()->content([]);
    }
}
