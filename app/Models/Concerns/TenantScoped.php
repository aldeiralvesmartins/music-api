<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait TenantScoped
{
    protected static function booted()
    {
        static::addGlobalScope('company', function (Builder $query) {
            $companyId = app()->bound('company_id') ? app('company_id') : null;
            if ($companyId) {
                $query->where($query->getModel()->getTable() . '.company_id', $companyId);
            }
        });

        static::creating(function ($model) {
            if (empty($model->company_id)) {
                $companyId = app()->bound('company_id') ? app('company_id') : null;
                if ($companyId) {
                    $model->company_id = $companyId;
                }
            }
        });
    }
}
