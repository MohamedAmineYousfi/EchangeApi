<?php

namespace App\Support\Traits;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Taggable
{
    /**
     * Get all of the tags for the post.
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * @param  $name
     */
    public function scopeTags(Builder $query, ?array $tags): Builder
    {
        return $query->whereHas('tags', function (Builder $subQuery) use ($tags) {
            $subQuery->whereIn('tags.id', $tags);
        });
    }
}
