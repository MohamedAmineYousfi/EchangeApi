<?php

namespace App\Http\Controllers\Api\V1\Import;

use App\Helpers\Import as HelpersImport;
use App\Helpers\ModelClass;
use App\Http\Requests\Api\V1\Import\RunRequest;
use App\Models\Import;
use CloudCreativity\LaravelJsonApi\Document\Error\Error;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class RunController extends JsonApiController
{
    public function run(Import $import, RunRequest $request): Response
    {
        try {
            $importData = HelpersImport::getImportData($import);
        } catch (Exception $e) {
            abort(400, $e->getMessage());
        }

        $results = [];

        DB::beginTransaction();
        try {
            foreach ($importData as $key => $data) {
                /** @var string */
                $modelClass = ModelClass::getModelInstanceByName($import->model);
                try {
                    $model = HelpersImport::createAndHydrateImportedModel($modelClass, $data, $import);
                    $existingObject = $modelClass::where($import->identifier['model_field'], $data[$import->identifier['model_field']])->first();
                    $modelId = $existingObject->id ?? $model->id; // @phpstan-ignore-line
                    if ($existingObject) {
                        $existingObject->fill($model->attributesToArray())->save();
                    } else {
                        $model->save();
                        DB::table('importables')->insert(
                            [
                                'import_id' => $import->id,
                                'importable_id' => $modelId,
                                'importable_type' => $model::class,
                            ]
                        );
                    }

                    $results[$key + 1] = [
                        'success' => true,
                        'line' => $key + 1,
                        'data' => $data,
                        'id' => $modelId,
                    ];
                } catch (Exception $e) {
                    $results[$key + 1] = [
                        'success' => false,
                        'line' => $key + 1,
                        'data' => $data,
                        'error' => $e->getMessage(),
                    ];
                }
            }
            $import->results = $results;
            $import->status = Import::STATUS_COMPLETED;
            $import->save();
            DB::commit();

            return $this->reply()->content($import);
        } catch (Exception $e) {
            DB::rollback();

            return $this->reply()->errors([
                Error::fromArray([
                    'title' => 'Transaction failed',
                    'detail' => $e->getMessage(),
                    'status' => '500',
                ]),
            ]);
        }
    }
}
