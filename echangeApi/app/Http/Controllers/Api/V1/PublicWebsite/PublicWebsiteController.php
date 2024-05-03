<?php

namespace App\Http\Controllers\Api\V1\PublicWebsite;

use App\Models\Auction;
use App\Models\Organization;
use App\Models\Property;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;
use Illuminate\Support\Carbon;

class PublicWebsiteController extends JsonApiController
{
    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function getProperties()
    {

        $today = Carbon::today();

        $properties = Property::whereHas('auction', function ($query) use ($today) {
                $query->where('end_at', '>=', $today) 
                ->orWhere(function ($query) use ($today) {
                    $query->where('start_at', '<=', $today)
                    ->where('end_at', '>=', $today);
                });
            })
            ->get();

        return $this->reply()->content($properties);
    }

    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function getActiveAuctions()
    {

        $today = Carbon::today();

        $activeAuctions = Auction::where('end_at', '>=', $today)
        ->orWhere(function ($query) use ($today) {
            $query->where('start_at', '<=', $today)
                ->where('end_at', '>=', $today);
        })
        ->get();

        return $this->reply()->content($activeAuctions);
    }

    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function getProperty(Property $property)
    {
        return $this->reply()->content($property);
    }

    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function getAuctions()
    {
        //évènement à venir

        $auctions = Auction::where('start_at', '>=', Carbon::today())
            ->get();

        return $this->reply()->content($auctions);
    }

    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function getOrganizations()
    {

        $organizations = Organization::all();
        return $this->reply()->content($organizations);
    }
}
