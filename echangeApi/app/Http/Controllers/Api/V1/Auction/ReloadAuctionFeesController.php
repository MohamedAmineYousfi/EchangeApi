<?php


namespace App\Http\Controllers\Api\V1\Auction;

use App\Models\Auction;
use App\Models\Property;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;

class ReloadAuctionFeesController extends JsonApiController
{
  /**
   * Handle the incoming request.
   *
   * @return mixed
   */
  public function reloadFees(Auction $auction)
  {
    $properties = $auction->properties;
    if ($properties->isEmpty()) {
      return abort(400, __('errors.no_property_found', []));
    } else {
      foreach ($properties as $property) {
        Property::assignMrcFees($property);
      }
      $auction->properties()->saveMany($properties);

      return $this->reply()->content($properties);
    }
  }
}