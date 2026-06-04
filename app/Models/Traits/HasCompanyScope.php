<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasCompanyScope
{
    public function scopeForCompany(Builder $query, int $companyId): Builder
    {
        return $query->where('company_id', $companyId);
    }
}
