<?php

namespace App\Helpers;

use App\Models\UnitOfMeasureUnit;
use Exception;

class UnitOfMeasure
{
    public static function convertQuantityToUnit(float $quantity, UnitOfMeasureUnit $sourceUnit, UnitOfMeasureUnit $destinationUnit)
    {
        if ($sourceUnit->unitOfMeasure->id != $destinationUnit->unitOfMeasure->id) {
            throw new Exception('SOURCE AND DESTINATION UNIT MUSt BE OF SAME UNIT OF MEASURE');
        }

        $referenceUnit = $sourceUnit->unitOfMeasure->getReferenceUnit();
        if ($sourceUnit->unit_type == UnitOfMeasureUnit::TYPE_SMALLER_THAN_REFERENCE) {
            $referenceQuantity = $quantity / $sourceUnit->ratio;
        } elseif ($sourceUnit->unit_type == UnitOfMeasureUnit::TYPE_BIGGER_THAN_REFERENCE) {
            $referenceQuantity = $quantity * $sourceUnit->ratio;
        } elseif ($sourceUnit->unit_type == UnitOfMeasureUnit::TYPE_REFERENCE_UNIT) {
            $referenceQuantity = $quantity;
        } else {
            throw new Exception('UNKNOWN CASE');
        }

        if ($destinationUnit->unit_type == UnitOfMeasureUnit::TYPE_SMALLER_THAN_REFERENCE) {
            $convertedQuantity = $referenceQuantity * $destinationUnit->ratio;
        } elseif ($destinationUnit->unit_type == UnitOfMeasureUnit::TYPE_BIGGER_THAN_REFERENCE) {
            $convertedQuantity = $referenceQuantity / $destinationUnit->ratio;
        } elseif ($destinationUnit->unit_type == UnitOfMeasureUnit::TYPE_REFERENCE_UNIT) {
            $convertedQuantity = $referenceQuantity;
        } else {
            throw new Exception('UNKNOWN CASE');
        }

        return $convertedQuantity;
    }
}
