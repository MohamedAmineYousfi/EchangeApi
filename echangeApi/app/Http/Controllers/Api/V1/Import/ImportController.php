<?php

namespace App\Http\Controllers\Api\V1\Import;

use App\Helpers\ModelClass;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;
use Doctrine\DBAL\Types\Type;
use Exception;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\MySqlBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;

class ImportController extends JsonApiController
{
    public function getModelInfoList(): JsonResponse
    {
        $models = $this->getAllModels();
        $data = [];

        foreach ($models as $model) {
            $attributes = $this->getModelAttributes(($model));
            array_push($data, [
                'model' => $model,
                'value' => $model,
                'key' => $model,
                'fields' => $attributes,
            ]);
        }

        return response()->json(['data' => $data], 200);
    }

    public function getAllModels(): array
    {
        $modelsPath = app_path('Models');
        $modelFiles = File::files($modelsPath);
        $modelNames = [];

        foreach ($modelFiles as $modelFile) {
            $modelName = pathinfo($modelFile, PATHINFO_FILENAME);
            $modelNames[] = $modelName;
        }

        $excludedColumns = ['Import', 'Log', 'Notification', 'Permission', 'Role'];

        return array_diff($modelNames, $excludedColumns);
    }

    public function getModelAttributes($modelClass): array
    {
        try {
            $modelClassName = ModelClass::getModelInstanceByName($modelClass);
        } catch (Exception $e) {
            abort(400, 'Error getting class : '.$modelClass.' <<<'.$e->getMessage().'>>>');
        }

        $tableName = (new $modelClassName)->getTable();
        /** @var MySqlBuilder */
        $schemaBuilder = app(Builder::class);

        $columnDetails = [];
        try {
            $columnDetails = $schemaBuilder->getConnection()->getDoctrineSchemaManager()->listTableColumns($tableName);
        } catch (\Exception $e) {
            abort(400, 'Error getting class : '.$modelClassName.' <<<'.$e->getMessage().'>>>');
        }

        $columnInfo = [];
        $excludedColumns = ['id', 'organization_id', 'created_at', 'updated_at', 'deleted_at'];
        foreach ($columnDetails as $name => $column) {
            if (in_array($name, $excludedColumns)) {
                continue;
            }
            array_push($columnInfo, [
                'required' => $column->getNotnull(),
                'code' => $name,
                'name' => $this->transformToHumanReadable($name),
                'type' => Type::lookupName($column->getType()),
            ]);
        }

        return $columnInfo;
    }

    private function transformToHumanReadable($input): string
    {
        $words = explode('_', $input);
        $capitalizedWords = array_map('ucfirst', $words);

        return implode(' ', $capitalizedWords);
    }
}
