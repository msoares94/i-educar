<?php

namespace App\Services;

use DateTime;

class AnthropometricService
{
    /**
     * Calcular IMC (Índice de Massa Corporal)
     *
     * @param float $peso Peso em kg
     * @param float $altura Altura em cm
     * @return float|null Valor do IMC ou null se entradas inválidas
     */
    public function calcularIMC(float $peso, float $altura): ?float
    {
        if ($peso <= 0 || $altura <= 0) {
            return null;
        }

        $alturaEmMetros = $altura / 100;
        return round($peso / ($alturaEmMetros * $alturaEmMetros), 2);
    }

    /**
     * Obter classificação do IMC para adultos (18+ anos)
     *
     * @param float $imc Valor do IMC
     * @return string Classificação do IMC
     */
    public function obterClassificacaoIMCAdulto(float $imc): string
    {
        if ($imc < 18.5) {
            return 'Baixo peso';
        } elseif ($imc < 25) {
            return 'Peso normal';
        } elseif ($imc < 30) {
            return 'Sobrepeso';
        } else {
            return 'Obesidade';
        }
    }

    /**
     * Obter classificação do IMC para crianças e adolescentes (5-19 anos)
     * Baseado nos padrões de crescimento da OMS
     *
     * @param float $imc Valor do IMC
     * @param int $idadeEmMeses Idade em meses
     * @param string $sexo 'M' para masculino, 'F' para feminino
     * @return string Classificação do IMC
     */
    public function obterClassificacaoIMCCrianca(float $imc, int $idadeEmMeses, string $sexo): string
    {
        $escoreZ = $this->calcularEscoreZIMC($imc, $idadeEmMeses, $sexo);

        if ($escoreZ === null) {
            return 'Não avaliável';
        }

        if ($escoreZ < -3) {
            return 'Magreza acentuada';
        } elseif ($escoreZ < -2) {
            return 'Magreza';
        } elseif ($escoreZ <= 1) {
            return 'Eutrofia';
        } elseif ($escoreZ <= 2) {
            return 'Sobrepeso';
        } elseif ($escoreZ <= 3) {
            return 'Obesidade';
        } else {
            return 'Obesidade grave';
        }
    }

    /**
     * Calcular Escore Z do IMC para crianças e adolescentes
     * Cálculo simplificado - em implementação real, usar tabelas de referência da OMS
     *
     * @param float $imc Valor do IMC
     * @param int $idadeEmMeses Idade em meses
     * @param string $sexo 'M' para masculino, 'F' para feminino
     * @return float|null Escore Z ou null se não puder calcular
     */
    private function calcularEscoreZIMC(float $imc, int $idadeEmMeses, string $sexo): ?float
    {
        // Valores de referência simplificados - em produção, usar tabelas reais da OMS
        $referencias = $this->obterReferenciasIMC($idadeEmMeses, $sexo);
        
        if (!$referencias) {
            return null;
        }

        // Cálculo simplificado do escore Z usando mediana e desvio padrão
        $mediana = $referencias['mediana'];
        $desvioPadrao = $referencias['desvio_padrao'];

        return round(($imc - $mediana) / $desvioPadrao, 2);
    }

    /**
     * Obter valores de referência simplificados do IMC por idade e sexo
     * Em produção, deve usar tabelas abrangentes da OMS
     *
     * @param int $idadeEmMeses Idade em meses
     * @param string $sexo 'M' para masculino, 'F' para feminino
     * @return array|null Valores de referência ou null se não disponível
     */
    private function obterReferenciasIMC(int $idadeEmMeses, string $sexo): ?array
    {
        // Valores de referência simplificados para demonstração
        // Em produção, usar tabelas abrangentes da OMS
        $referencias = [
            // Idades 5-10 anos (60-120 meses)
            60 => ['M' => ['mediana' => 15.3, 'desvio_padrao' => 1.2], 'F' => ['mediana' => 15.2, 'desvio_padrao' => 1.3]],
            72 => ['M' => ['mediana' => 15.6, 'desvio_padrao' => 1.3], 'F' => ['mediana' => 15.5, 'desvio_padrao' => 1.4]],
            84 => ['M' => ['mediana' => 15.9, 'desvio_padrao' => 1.4], 'F' => ['mediana' => 15.8, 'desvio_padrao' => 1.5]],
            96 => ['M' => ['mediana' => 16.2, 'desvio_padrao' => 1.5], 'F' => ['mediana' => 16.1, 'desvio_padrao' => 1.6]],
            108 => ['M' => ['mediana' => 16.5, 'desvio_padrao' => 1.6], 'F' => ['mediana' => 16.4, 'desvio_padrao' => 1.7]],
            120 => ['M' => ['mediana' => 16.8, 'desvio_padrao' => 1.7], 'F' => ['mediana' => 16.7, 'desvio_padrao' => 1.8]],
            // Idades 10-15 anos (120-180 meses)
            132 => ['M' => ['mediana' => 17.2, 'desvio_padrao' => 1.8], 'F' => ['mediana' => 17.1, 'desvio_padrao' => 1.9]],
            144 => ['M' => ['mediana' => 17.8, 'desvio_padrao' => 2.0], 'F' => ['mediana' => 17.9, 'desvio_padrao' => 2.1]],
            156 => ['M' => ['mediana' => 18.5, 'desvio_padrao' => 2.2], 'F' => ['mediana' => 18.8, 'desvio_padrao' => 2.3]],
            168 => ['M' => ['mediana' => 19.3, 'desvio_padrao' => 2.4], 'F' => ['mediana' => 19.6, 'desvio_padrao' => 2.5]],
            180 => ['M' => ['mediana' => 20.1, 'desvio_padrao' => 2.6], 'F' => ['mediana' => 20.3, 'desvio_padrao' => 2.7]],
            // Idades 15-19 anos (180-228 meses)
            192 => ['M' => ['mediana' => 20.8, 'desvio_padrao' => 2.8], 'F' => ['mediana' => 20.9, 'desvio_padrao' => 2.9]],
            204 => ['M' => ['mediana' => 21.4, 'desvio_padrao' => 3.0], 'F' => ['mediana' => 21.4, 'desvio_padrao' => 3.1]],
            216 => ['M' => ['mediana' => 21.9, 'desvio_padrao' => 3.2], 'F' => ['mediana' => 21.8, 'desvio_padrao' => 3.3]],
            228 => ['M' => ['mediana' => 22.3, 'desvio_padrao' => 3.4], 'F' => ['mediana' => 22.1, 'desvio_padrao' => 3.5]],
        ];

        // Encontrar a idade de referência mais próxima
        $idadeMaisProxima = null;
        $menorDiferenca = PHP_INT_MAX;
        
        foreach ($referencias as $idadeRef => $valores) {
            $diferenca = abs($idadeEmMeses - $idadeRef);
            if ($diferenca < $menorDiferenca) {
                $menorDiferenca = $diferenca;
                $idadeMaisProxima = $idadeRef;
            }
        }

        if ($idadeMaisProxima && isset($referencias[$idadeMaisProxima][$sexo])) {
            return $referencias[$idadeMaisProxima][$sexo];
        }

        return null;
    }

    /**
     * Obter classificação de risco da circunferência da cintura
     *
     * @param float $circunferenciaCintura Circunferência da cintura em cm
     * @param string $sexo 'M' para masculino, 'F' para feminino
     * @param int|null $idade Idade em anos (null para adultos)
     * @return string Classificação de risco
     */
    public function obterClassificacaoRiscoCircunferenciaCintura(float $circunferenciaCintura, string $sexo, ?int $idade = null): string
    {
        // Para adultos (18+ anos)
        if ($idade === null || $idade >= 18) {
            return $this->obterRiscoCinturaAdulto($circunferenciaCintura, $sexo);
        }

        // Para crianças e adolescentes
        return $this->obterRiscoCinturaCrianca($circunferenciaCintura, $sexo, $idade);
    }

    /**
     * Obter risco da circunferência da cintura para adultos
     *
     * @param float $circunferenciaCintura Circunferência da cintura em cm
     * @param string $sexo 'M' para masculino, 'F' para feminino
     * @return string Classificação de risco
     */
    private function obterRiscoCinturaAdulto(float $circunferenciaCintura, string $sexo): string
    {
        if ($sexo === 'M') {
            if ($circunferenciaCintura < 94) {
                return 'Sem risco';
            } elseif ($circunferenciaCintura < 102) {
                return 'Risco aumentado';
            } else {
                return 'Risco muito aumentado';
            }
        } else { // Feminino
            if ($circunferenciaCintura < 80) {
                return 'Sem risco';
            } elseif ($circunferenciaCintura < 88) {
                return 'Risco aumentado';
            } else {
                return 'Risco muito aumentado';
            }
        }
    }

    /**
     * Obter risco da circunferência da cintura para crianças e adolescentes
     * Classificação simplificada - em produção, usar tabelas de percentis
     *
     * @param float $circunferenciaCintura Circunferência da cintura em cm
     * @param string $sexo 'M' para masculino, 'F' para feminino
     * @param int $idade Idade em anos
     * @return string Classificação de risco
     */
    private function obterRiscoCinturaCrianca(float $circunferenciaCintura, string $sexo, int $idade): string
    {
        // Classificação simplificada baseada em percentis
        // Em produção, usar tabelas reais de percentis por idade e sexo
        $percentil90 = $this->obterPercentil90Cintura($idade, $sexo);
        
        if ($circunferenciaCintura >= $percentil90) {
            return 'Risco aumentado (≥ P90)';
        } else {
            return 'Sem risco (< P90)';
        }
    }

    /**
     * Obter percentil 90 simplificado da circunferência da cintura por idade e sexo
     *
     * @param int $idade Idade em anos
     * @param string $sexo 'M' para masculino, 'F' para feminino
     * @return float Valor do percentil 90
     */
    private function obterPercentil90Cintura(int $idade, string $sexo): float
    {
        // Valores de percentil simplificados - em produção, usar tabelas abrangentes
        $percentis = [
            5 => ['M' => 56, 'F' => 55],
            6 => ['M' => 58, 'F' => 57],
            7 => ['M' => 60, 'F' => 59],
            8 => ['M' => 62, 'F' => 61],
            9 => ['M' => 64, 'F' => 63],
            10 => ['M' => 67, 'F' => 66],
            11 => ['M' => 70, 'F' => 69],
            12 => ['M' => 73, 'F' => 72],
            13 => ['M' => 76, 'F' => 75],
            14 => ['M' => 79, 'F' => 78],
            15 => ['M' => 82, 'F' => 81],
            16 => ['M' => 85, 'F' => 83],
            17 => ['M' => 88, 'F' => 85],
        ];

        if (isset($percentis[$idade][$sexo])) {
            return $percentis[$idade][$sexo];
        }

        // Valores padrão para idades não na tabela
        return $sexo === 'M' ? 90 : 87;
    }

    /**
     * Calcular idade em meses a partir da data de nascimento
     *
     * @param string|DateTime $dataNascimento Data de nascimento
     * @param string|DateTime|null $dataReferencia Data de referência (padrão: hoje)
     * @return int Idade em meses
     */
    public function calcularIdadeEmMeses($dataNascimento, $dataReferencia = null): int
    {
        if (!$dataNascimento instanceof DateTime) {
            $dataNascimento = new DateTime($dataNascimento);
        }

        if (!$dataReferencia) {
            $dataReferencia = new DateTime();
        } elseif (!$dataReferencia instanceof DateTime) {
            $dataReferencia = new DateTime($dataReferencia);
        }

        $intervalo = $dataNascimento->diff($dataReferencia);
        return ($intervalo->y * 12) + $intervalo->m;
    }

    /**
     * Calcular idade em anos a partir da data de nascimento
     *
     * @param string|DateTime $dataNascimento Data de nascimento
     * @param string|DateTime|null $dataReferencia Data de referência (padrão: hoje)
     * @return int Idade em anos
     */
    public function calcularIdadeEmAnos($dataNascimento, $dataReferencia = null): int
    {
        if (!$dataNascimento instanceof DateTime) {
            $dataNascimento = new DateTime($dataNascimento);
        }

        if (!$dataReferencia) {
            $dataReferencia = new DateTime();
        } elseif (!$dataReferencia instanceof DateTime) {
            $dataReferencia = new DateTime($dataReferencia);
        }

        return $dataNascimento->diff($dataReferencia)->y;
    }

    /**
     * Obter avaliação antropométrica completa
     *
     * @param float $peso Peso em kg
     * @param float $altura Altura em cm
     * @param float|null $circunferenciaCintura Circunferência da cintura em cm (opcional)
     * @param string|DateTime $dataNascimento Data de nascimento
     * @param string $sexo 'M' para masculino, 'F' para feminino
     * @param string|DateTime|null $dataAvaliacao Data da avaliação (padrão: hoje)
     * @return array Dados completos da avaliação
     */
    public function obterAvaliacaoCompleta(
        float $peso,
        float $altura,
        ?float $circunferenciaCintura,
        $dataNascimento,
        string $sexo,
        $dataAvaliacao = null
    ): array {
        $imc = $this->calcularIMC($peso, $altura);
        $idadeEmAnos = $this->calcularIdadeEmAnos($dataNascimento, $dataAvaliacao);
        $idadeEmMeses = $this->calcularIdadeEmMeses($dataNascimento, $dataAvaliacao);

        $avaliacao = [
            'peso' => $peso,
            'altura' => $altura,
            'circunferencia_cintura' => $circunferenciaCintura,
            'imc' => $imc,
            'idade_anos' => $idadeEmAnos,
            'idade_meses' => $idadeEmMeses,
            'sexo' => $sexo,
        ];

        if ($imc !== null) {
            if ($idadeEmAnos >= 18) {
                $avaliacao['classificacao_imc'] = $this->obterClassificacaoIMCAdulto($imc);
            } else {
                $avaliacao['classificacao_imc'] = $this->obterClassificacaoIMCCrianca($imc, $idadeEmMeses, $sexo);
                $avaliacao['escore_z_imc'] = $this->calcularEscoreZIMC($imc, $idadeEmMeses, $sexo);
            }
        }

        if ($circunferenciaCintura !== null) {
            $avaliacao['risco_cintura'] = $this->obterClassificacaoRiscoCircunferenciaCintura(
                $circunferenciaCintura,
                $sexo,
                $idadeEmAnos
            );
        }

        return $avaliacao;
    }
}