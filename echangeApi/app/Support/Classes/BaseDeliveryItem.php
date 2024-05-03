<?php

namespace App\Support\Classes;

use App\Support\Interfaces\Deliverable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property string $code
 * @property int $quantity
 * @property string $excerpt
 */
abstract class BaseDeliveryItem extends Model
{
    use LogsActivity;
    use SoftDeletes;

    /************ Abstract functions ************/
    abstract public function getDelivery();

    abstract public function getDeliverable(): Deliverable;

    /************ Common functions, attributes and constants ************/
    protected $fillable = [
        'code',
        'expected_quantity',
        'quantity',
        'excerpt',
    ];

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $casts = [];

    /**
     * getActivitylogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
}
