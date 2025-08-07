<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Department;
use App\Models\Company;
use App\Scopes\CompanyScope;

class DisposalAsset extends Model
{
    use HasFactory;

    protected $table = 'disposal_assets';

    protected $fillable = [
        'submit_date',
        'form_no',
        'department_id',
        'reason',
        'nbv',
        'esp',
        'company_id',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope);
    }
}
