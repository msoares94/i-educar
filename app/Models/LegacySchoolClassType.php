<?php

namespace App\Models;

use App\Traits\Ativo;
use App\Traits\HasInstitution;

class LegacySchoolClassType extends LegacyModel
{
    use Ativo;
    use HasInstitution;

    public const CREATED_AT = 'data_cadastro';
    public const UPDATED_AT = null;

    /**
     * @var string
     */
    protected $table = 'pmieducar.turma_tipo';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_turma_tipo';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_usuario_cad',
        'nm_tipo',
        'sgl_tipo',
        'ativo'
    ];
}
