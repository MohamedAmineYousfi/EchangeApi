<?php

namespace App\Http\Controllers\Api\V1\AppConfig;

use App\Http\Requests\Api\V1\AppConfig\GetAppConfigRequest;
use App\Models\Reseller;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;

class GetAppConfigController extends JsonApiController
{
    /**
     * @return JsonResponse|Response
     */
    public function __invoke(GetAppConfigRequest $request)
    {
        $origin = $request->header('origin');
        $appConfig = null;
        try {
            $resellers = Reseller::all();
            foreach ($resellers as $reseller) {
                if ($reseller->config_manager_url_regex) {
                    if (preg_match("/{$reseller->config_manager_url_regex}/", $origin)) {
                        foreach ($reseller->getFillable() as $field) {
                            if (str_starts_with($field, 'config_')) {
                                $appConfig[$field] = $reseller->$field;
                            }
                        }
                    }
                }
            }

            return response()->json($appConfig);
        } catch (Exception $e) {
            return response()->json($this->getDefaultAppConfig());
        }
    }

    public function getDefaultAppConfig()
    {
        return [
            'config_manager_app_name' => env('DEFAULT_CONFIG_MANAGER_APP_NAME'),
            'config_manager_app_logo' => URL::asset(env('DEFAULT_CONFIG_MANAGER_APP_LOGO')),
            'config_manager_url_regex' => '.*',
        ];
    }
}
