<?php

namespace App\Support\Traits;

use App\Models\Reseller;
use App\Models\Scopes\ResellerScope;
use App\Models\User;
use App\Support\Interfaces\ResellerScopable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait ResellerScoped
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new ResellerScope());

        static::saving(function (ResellerScopable $model) {
            /** @var ?User */
            $user = auth()->user();
            if ($user) {
                if ($user->reseller) {
                    if (! $model->getReseller()) {
                        $model->reseller()->associate($user->reseller);
                    } else {
                        if (! $model->getReseller()->is($user->reseller)) {
                            abort(403, 'You are not allowed to edit this model');
                        }
                    }
                }
            }
        });
    }

    /**
     * Undocumented function
     */
    public function getReseller(): ?Reseller
    {
        return $this->reseller;
    }

    /**
     * Undocumented function
     */
    public function reseller(): BelongsTo
    {
        return $this->belongsTo(Reseller::class);
    }

    /**
     * Undocumented function
     */
    public function scopeReseller(Builder $query, ?string $reseller): Builder
    {
        if ($reseller) {
            return $query->where($query->getModel()->getTable().'.reseller_id', '=', $reseller, 'and');
        } else {
            return $query->whereNull($query->getModel()->getTable().'.reseller_id');
        }
    }
}
