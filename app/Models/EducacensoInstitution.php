<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EducacensoInstitution extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.educacenso_ies';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'ies_id',
        'nome',
        'dependencia_administrativa_id',
        'tipo_instituicao_id',
        'uf',
        'user_id',
        'created_at',
    ];

    /**
     * @return HasMany<LegacySchool, $this>
     */
    public function schools(): HasMany
    {
        return $this->hasMany(LegacySchool::class, 'codigo_ies');
    }

    /**
     * @return HasMany<EmployeeGraduation, $this>
     */
    public function employeeGraduations(): HasMany
    {
        return $this->hasMany(EmployeeGraduation::class, 'college_id');
    }

    /**
     * @return HasMany<Employee, $this>
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'instituicao_curso_superior_3');
    }
}
