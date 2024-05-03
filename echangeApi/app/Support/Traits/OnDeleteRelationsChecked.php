<?php

namespace App\Support\Traits;

use App\Support\Interfaces\OnDeleteRelationsCheckable;

trait OnDeleteRelationsChecked
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::deleting(function (OnDeleteRelationsCheckable $model) {
            $relationMethods = $model->getRelationsMethods();

            foreach ($relationMethods as $relationMethod) {
                if ($model->$relationMethod()->count() > 0) {
                    abort(400, __('errors.delete_model_has_relations', ['model' => class_basename($model), 'relation' => $relationMethod]));
                }
            }
        });
    }
}
