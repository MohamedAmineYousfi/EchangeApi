<?php

namespace App\Helpers;

use Exception;

class ModelClass
{
    /**
     * @throws Exception
     */
    public static function getModelInstanceByName($modelName): string
    {
        $modelClass = '\\App\\Models\\'.$modelName;

        if (! class_exists($modelClass)) {
            throw new Exception('This does not exist');
        }

        return $modelClass;
    }
}
