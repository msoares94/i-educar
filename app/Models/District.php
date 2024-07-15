<?php

namespace App\Models;

use App\Models\Builders\DistrictBuilder;
use App\Models\Concerns\HasIbgeCode;
use App\Support\Database\DateSerializer;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class District extends Model
{
    use DateSerializer;
    use HasBuilder;
    use HasIbgeCode;

    /**
     * Builder dos filtros
     */
    protected static string $builder = DistrictBuilder::class;

    /**
     * @var array
     */
    protected $fillable = [
        'city_id',
        'name',
        'ibge_code',
    ];

    /**
     * @return BelongsTo
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
