<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Activity;

class Log extends Activity
{
    use SoftDeletes;

    public function scopeSearch($query, $search): Builder
    {
        return $query->where('activity_log.log_name', 'LIKE', "%$search%", 'or')
            ->where('activity_log.description', 'LIKE', "%$search%", 'or')
            ->where('activity_log.properties', 'LIKE', "%$search%", 'or');
    }

    /**
     * Scope the query for created_at between dates
     *
     * @param  Builder  $query
     */
    public function scopeSubjectType($query, $subjectType): Builder
    {
        $type = json_api()->getDefaultResolver()->getType($subjectType);

        return $query->where('activity_log.subject_type', $type);
    }

    /**
     * Scope the query for created_at between dates
     *
     * @param  Builder  $query
     * @param  array  $dates
     */
    public function scopeCreatedAtBetween($query, $dates): Builder
    {
        return $query->whereBetween('activity_log.created_at', $dates);
    }
}
