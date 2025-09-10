<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Scopes\CompanyScope;
use App\Models\Company;

class PersonInCharge extends Model
{
    use HasFactory;

    protected $table = 'person_in_charges';

    protected $fillable = [
        'name',
        'position',
        'company_id',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope);
    }
}
