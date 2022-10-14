<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Builders\LegacyBuilder;
use Illuminate\Database\Eloquent\Model;

class LegacyModel extends Model
{
    public array $legacy = [];

    protected string $builder = LegacyBuilder::class;

    public function __get($key)
    {
        return parent::__get($this->getLegacyColumn($key));
    }

    public function __set($key, $value)
    {
        parent::__set($this->getLegacyColumn($key), $value);
    }

    public function newEloquentBuilder($query)
    {
        return new $this->builder($query);
    }

    public function attributesToArray()
    {
        if (property_exists($this, 'legacy')) {
            $legacy = array_flip($this->legacy);
            $newAttributes = [];
            foreach (parent::attributesToArray() as $key => $value) {
                $newAttributes[$legacy[$key] ?? $key] = $value;
            }

            return $newAttributes;
        }

        return parent::attributesToArray();
    }

    public function fill(array $attributes)
    {
        if (property_exists($this, 'legacy')) {
            foreach ($attributes as $key => $value) {
                $this->setAttribute($this->getLegacyColumn($key), $value);
            }

            return $this;
        }

        return parent::fill($attributes);
    }

    public function getLegacyColumn($key)
    {
        return $this->legacy[$key] ?? $key;
    }
}
