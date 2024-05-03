<?php

namespace App\Helpers;

use App\Models\Import as ModelsImport;
use App\Support\Interfaces\OrganizationScopable;
use Doctrine\DBAL\Types\JsonType;
use DragonCode\Support\Facades\Http\Url;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\MySqlBuilder;
use Illuminate\Support\Facades\File;
use League\Csv\Info;
use League\Csv\Reader;
use League\Csv\Statement;

class Import
{
    public static function getImportData(ModelsImport $import)
    {
        $urlFilePath = Url::parse($import->file_url)->getPath();
        $urlFilePath = str_replace('/storage', '', $urlFilePath);
        $importFilePath = storage_path("app/public$urlFilePath");

        if (! File::exists($importFilePath)) {
            throw new Exception(__('errors.IMPORT_FILE_NOT_FOUND'));
        }

        /** @var Reader */
        $csvData = Reader::createFromPath($importFilePath);
        $delimiter = self::inferCsvDelimiter($csvData);
        $csvData->setDelimiter($delimiter);
        $csvData->setHeaderOffset(0);
        $stmt = Statement::create();
        $records = $stmt->process($csvData);

        $importData = [];
        foreach ($records as $record) {
            $line = [];
            foreach ($import->mapping as $objField => $csvField) {
                $line[$objField] = $record[$csvField];
            }
            $importData[] = $line;
        }

        return $importData;
    }

    public static function inferCsvDelimiter(Reader $csvData)
    {
        $delimiterStats = Info::getDelimiterStats($csvData, [',', ';'], -1);
        arsort($delimiterStats, 1);

        return array_keys($delimiterStats)[0];
    }

    public static function extractFloatFromString($inputString): float
    {
        preg_match('/[\d,]+[.]?[\d,]*/', $inputString, $matches);
        $cleanedNumber = str_replace(',', '.', $matches[0]);

        return (float) $cleanedNumber;
    }

    public static function escapeSpecialChars($value): string
    {
        return htmlspecialchars($value);
    }

    public static function createAndHydrateImportedModel(string $modelClass, array $data, ModelsImport $import): Model
    {
        $model = new $modelClass;

        /** @var MySqlBuilder */
        $schemaBuilder = app(Builder::class);
        try {
            $columnDetails = $schemaBuilder->getConnection()->getDoctrineSchemaManager()->listTableColumns($model->getTable());
        } catch (\Exception $e) {
            abort(400, 'Error getting class : '.$modelClass.' <<<'.$e->getMessage().'>>>');
        }

        foreach ($data as $field => $value) {
            if ($columnDetails[$field]->getType() instanceof JsonType) {
                $jsonString = str_replace('|', ',', $value);
                $jsonString = preg_replace('/}\s*{/', '},{', $jsonString);
                $data = json_decode($jsonString, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $model->$field = $data;
                } else {
                    abort(400, 'Error invalid json data. example: `[{"name": "TPS (5%)"|"type": "PERCENTAGE"|"value": 5}| {"name": "TVQ (9.975%)"|"type": "PERCENTAGE"|"value": 9.975}]`');
                }
            } elseif ($columnDetails[$field]->getType()->getName() === 'float' || $columnDetails[$field]->getType()->getName() === 'double' || $columnDetails[$field]->getType()->getName() === 'numeric') {
                $model->$field = Import::extractFloatFromString($value);
            } else {
                $model->$field = Import::escapeSpecialChars($value);
            }
        }

        if ($model instanceof OrganizationScopable) {
            $model->organization()->associate($import->organization);
        }

        return $model;
    }
}
