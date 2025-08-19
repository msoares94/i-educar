<?php

namespace Tests\Unit\Services;

use App\Services\AnthropometricService;
use DateTime;
use PHPUnit\Framework\TestCase;

class AnthropometricServiceTest extends TestCase
{
    private AnthropometricService $servico;

    protected function setUp(): void
    {
        parent::setUp();
        $this->servico = new AnthropometricService();
    }

    public function testCalcularIMC()
    {
        // Teste cálculo normal do IMC
        $imc = $this->servico->calcularIMC(70, 175);
        $this->assertEquals(22.86, $imc);

        // Teste com valores diferentes
        $imc = $this->servico->calcularIMC(80, 180);
        $this->assertEquals(24.69, $imc);

        // Teste casos extremos
        $this->assertNull($this->servico->calcularIMC(0, 175));
        $this->assertNull($this->servico->calcularIMC(70, 0));
        $this->assertNull($this->servico->calcularIMC(-10, 175));
        $this->assertNull($this->servico->calcularIMC(70, -175));
    }

    public function testObterClassificacaoIMCAdulto()
    {
        $this->assertEquals('Baixo peso', $this->servico->obterClassificacaoIMCAdulto(17.5));
        $this->assertEquals('Peso normal', $this->servico->obterClassificacaoIMCAdulto(22.0));
        $this->assertEquals('Sobrepeso', $this->servico->obterClassificacaoIMCAdulto(27.0));
        $this->assertEquals('Obesidade', $this->servico->obterClassificacaoIMCAdulto(32.0));
    }

    public function testObterClassificacaoIMCCrianca()
    {
        // Teste para menino de 10 anos (120 meses)
        $classificacao = $this->servico->obterClassificacaoIMCCrianca(16.8, 120, 'M');
        $this->assertEquals('Eutrofia', $classificacao);

        // Teste para menina de 12 anos (144 meses)
        $classificacao = $this->servico->obterClassificacaoIMCCrianca(20.0, 144, 'F');
        $this->assertEquals('Eutrofia', $classificacao);

        // Teste caso extremo - IMC muito alto
        $classificacao = $this->servico->obterClassificacaoIMCCrianca(25.0, 120, 'M');
        $this->assertEquals('Obesidade grave', $classificacao);
    }

    public function testObterClassificacaoRiscoCircunferenciaCinturaAdulto()
    {
        // Teste homem adulto
        $this->assertEquals('Sem risco', $this->servico->obterClassificacaoRiscoCircunferenciaCintura(90, 'M', 25));
        $this->assertEquals('Risco aumentado', $this->servico->obterClassificacaoRiscoCircunferenciaCintura(98, 'M', 25));
        $this->assertEquals('Risco muito aumentado', $this->servico->obterClassificacaoRiscoCircunferenciaCintura(105, 'M', 25));

        // Teste mulher adulta
        $this->assertEquals('Sem risco', $this->servico->obterClassificacaoRiscoCircunferenciaCintura(75, 'F', 25));
        $this->assertEquals('Risco aumentado', $this->servico->obterClassificacaoRiscoCircunferenciaCintura(85, 'F', 25));
        $this->assertEquals('Risco muito aumentado', $this->servico->obterClassificacaoRiscoCircunferenciaCintura(95, 'F', 25));
    }

    public function testObterClassificacaoRiscoCircunferenciaCinturaCrianca()
    {
        // Teste criança no percentil 90 ou acima
        $this->assertEquals('Risco aumentado (≥ P90)', $this->servico->obterClassificacaoRiscoCircunferenciaCintura(70, 'M', 10));
        $this->assertEquals('Sem risco (< P90)', $this->servico->obterClassificacaoRiscoCircunferenciaCintura(60, 'M', 10));
    }

    public function testCalcularIdadeEmMeses()
    {
        $dataNascimento = new DateTime('2010-01-01');
        $dataReferencia = new DateTime('2020-01-01');
        
        $idadeEmMeses = $this->servico->calcularIdadeEmMeses($dataNascimento, $dataReferencia);
        $this->assertEquals(120, $idadeEmMeses); // 10 anos = 120 meses

        // Teste com datas em string
        $idadeEmMeses = $this->servico->calcularIdadeEmMeses('2010-06-01', '2020-06-01');
        $this->assertEquals(120, $idadeEmMeses);

        // Teste com meses parciais
        $idadeEmMeses = $this->servico->calcularIdadeEmMeses('2010-01-01', '2020-07-01');
        $this->assertEquals(126, $idadeEmMeses); // 10 anos e 6 meses
    }

    public function testCalcularIdadeEmAnos()
    {
        $dataNascimento = new DateTime('2010-01-01');
        $dataReferencia = new DateTime('2020-01-01');
        
        $idadeEmAnos = $this->servico->calcularIdadeEmAnos($dataNascimento, $dataReferencia);
        $this->assertEquals(10, $idadeEmAnos);

        // Teste com datas em string
        $idadeEmAnos = $this->servico->calcularIdadeEmAnos('2010-06-01', '2020-07-01');
        $this->assertEquals(10, $idadeEmAnos);

        // Teste ano bissexto
        $idadeEmAnos = $this->servico->calcularIdadeEmAnos('2000-02-29', '2020-02-28');
        $this->assertEquals(19, $idadeEmAnos);
    }

    public function testObterAvaliacaoCompletaAdulto()
    {
        $avaliacao = $this->servico->obterAvaliacaoCompleta(
            peso: 70.0,
            altura: 175.0,
            circunferenciaCintura: 85.0,
            dataNascimento: '1990-01-01',
            sexo: 'M',
            dataAvaliacao: '2020-01-01'
        );

        $this->assertEquals(70.0, $avaliacao['peso']);
        $this->assertEquals(175.0, $avaliacao['altura']);
        $this->assertEquals(85.0, $avaliacao['circunferencia_cintura']);
        $this->assertEquals(22.86, $avaliacao['imc']);
        $this->assertEquals(30, $avaliacao['idade_anos']);
        $this->assertEquals('M', $avaliacao['sexo']);
        $this->assertEquals('Peso normal', $avaliacao['classificacao_imc']);
        $this->assertEquals('Sem risco', $avaliacao['risco_cintura']);
        $this->assertArrayNotHasKey('escore_z_imc', $avaliacao); // Adultos não têm escore Z
    }

    public function testObterAvaliacaoCompletaCrianca()
    {
        $avaliacao = $this->servico->obterAvaliacaoCompleta(
            peso: 35.0,
            altura: 140.0,
            circunferenciaCintura: 65.0,
            dataNascimento: '2010-01-01',
            sexo: 'M',
            dataAvaliacao: '2020-01-01'
        );

        $this->assertEquals(35.0, $avaliacao['peso']);
        $this->assertEquals(140.0, $avaliacao['altura']);
        $this->assertEquals(65.0, $avaliacao['circunferencia_cintura']);
        $this->assertEquals(17.86, $avaliacao['imc']);
        $this->assertEquals(10, $avaliacao['idade_anos']);
        $this->assertEquals(120, $avaliacao['idade_meses']);
        $this->assertEquals('M', $avaliacao['sexo']);
        $this->assertIsString($avaliacao['classificacao_imc']);
        $this->assertIsFloat($avaliacao['escore_z_imc']);
        $this->assertEquals('Sem risco (< P90)', $avaliacao['risco_cintura']);
    }

    public function testObterAvaliacaoCompletaSemCircunferenciaCintura()
    {
        $avaliacao = $this->servico->obterAvaliacaoCompleta(
            peso: 70.0,
            altura: 175.0,
            circunferenciaCintura: null,
            dataNascimento: '1990-01-01',
            sexo: 'M',
            dataAvaliacao: '2020-01-01'
        );

        $this->assertNull($avaliacao['circunferencia_cintura']);
        $this->assertArrayNotHasKey('risco_cintura', $avaliacao);
    }

    public function testObterAvaliacaoCompletaComIMCInvalido()
    {
        $avaliacao = $this->servico->obterAvaliacaoCompleta(
            peso: 0.0,
            altura: 175.0,
            circunferenciaCintura: 85.0,
            dataNascimento: '1990-01-01',
            sexo: 'M',
            dataAvaliacao: '2020-01-01'
        );

        $this->assertNull($avaliacao['imc']);
        $this->assertArrayNotHasKey('classificacao_imc', $avaliacao);
        $this->assertArrayNotHasKey('escore_z_imc', $avaliacao);
    }
}