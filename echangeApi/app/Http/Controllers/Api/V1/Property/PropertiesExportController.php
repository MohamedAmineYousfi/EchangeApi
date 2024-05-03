<?php

namespace App\Http\Controllers\Api\V1\Property;

use App\Constants\Permissions;
use App\Exports\PropertiesExport;
use App\Models\Property;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;

class PropertiesExportController extends JsonApiController
{
    /**
     * Handle the incoming request.
     *
     * @return Application|ResponseFactory|Response
     */
    public function exports()
    {

        if (! auth()->user()->can(Permissions::PERM_EXPORTS_PROPERTIES)) {
            abort(403, __('notifications.unauthorized_to_export_properties', []));
        }
        $requestData = json_decode(request()->input('filter'), true);
        [
            'organization' => $organization,
            'active' => $active,
            'search' => $search,
            'onlyConfirmed' => $onlyConfirmed,
            'auction' => $auction,
            'allowedLocations' => $allowedLocations
        ] = $requestData + [
            'organization' => null,
            'auction' => null,
            'active' => null,
            'search' => null,
            'onlyConfirmed' => null,
            'allowedLocations' => null,
        ];

        $query = Property::query();

        if (isset($organization)) {
            $query->organization($organization);
        }

        if (isset($active)) {
            $query->active($active);
        }

        if (isset($search)) {
            $query->search($search);
        }

        if (isset($auction)) {
            $query->auction($auction);
        }

        if (isset($onlyConfirmed)) {
            $query->whereNotNull('sale_confirmed_at');
        }

        if (isset($allowedLocations)) {
            if (count($allowedLocations)) {
                $query->allowedLocations($allowedLocations);
            }
        }

        $properties = $query->get();
        $properties = $query->orderBy('property_number', 'asc')->get();
        $export = new PropertiesExport($properties);
        $file = Excel::raw($export, \Maatwebsite\Excel\Excel::XLSX);

        return response($file, 200)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', 'attachment; filename="properties_'.now()->format('d/m/Y_H_i_s').'.xlsx"');
    }
}
