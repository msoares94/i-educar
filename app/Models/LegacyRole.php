<?php

namespace App\Models;

use App\Models\Builders\LegacyRoleBuilder;
use App\Models\Concerns\SoftDeletes\LegacySoftDeletes;
use App\Traits\HasInstitution;
use App\Traits\HasLegacyDates;
use App\Traits\HasLegacyUserAction;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegacyRole extends LegacyModel
{
    use HasInstitution;
    use HasLegacyDates;
    use HasLegacyUserAction;
    use LegacySoftDeletes;
    /** @use HasBuilder<LegacyRoleBuilder> */
    use HasBuilder;

    protected $table = 'pmieducar.funcao';

    protected $primaryKey = 'cod_funcao';

    protected static string $builder = LegacyRoleBuilder::class;


    protected $fillable = [
        'nm_funcao',
        'abreviatura',
        'professor',
    ];

    protected function id(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->cod_funcao
        );
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->nm_funcao
        );
    }

    public function employeeRoles(): HasMany
    {
        return $this->hasMany(LegacyEmployeeRole::class, 'ref_cod_funcao');
    }
}
