<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\TenantScoped;

class Image extends Model
{
    use TenantScoped;
    protected $fillable = ['url', 'company_id'];
    public $timestamps = false;

    public function imageable()
    {
        return $this->morphTo();
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
