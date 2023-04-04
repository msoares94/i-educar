<?php

namespace App\Models\Builders;

use Illuminate\Support\Collection;

class LegacyEvaluationRuleBuilder extends LegacyBuilder
{
    /**
     * Retorna o recurso para os selects dos formulários
     *
     * @param array $filters
     *
     * @return Collection
     */
    public function getResource(array $filters = []): Collection
    {
        $this->orderByName()->filter($filters);

        return $this->resource(['id', 'name']);
    }

    /**
     * Ordena por nome
     *
     * @param string $direction
     *
     * @return LegacyEvaluationRuleBuilder
     */
    public function orderByName(string $direction = 'asc'): self
    {
        return $this->orderBy('nome', $direction);
    }

    /**
     * Filtra por Instituição
     *
     * @param int $institution
     *
     * @return LegacyEvaluationRuleBuilder
     */
    public function whereInstitution(int $institution): self
    {
        return $this->where('instituicao_id', $institution);
    }
}
