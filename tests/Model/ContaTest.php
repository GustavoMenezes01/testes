<?php

use PHPUnit\Framework\TestCase;

class ContaTest extends TestCase
{

    /**
     *  @dataProvider geraTitular
     *  @covers Titular::recuperaCpf
     *  @covers Titular::recuperaNome
     *  @covers CPF::__construct
     *  @covers Conta::__construct
     *  @covers Conta::__destruct
     *  @covers Conta::recuperaNomeTitular
     *  @covers Titular::__construct
     *  @covers Titular::validaNomeTitular

     */
    public function testNomeDoTitularPrecisaSerValido(Titular $titular)
    {
        $titularTeste = new Titular(new Cpf('123.456.789-10'), 'João da Silva Sauro');
        $conta = new Conta($titularTeste);
        static::assertEquals($titular->recuperaNome(), $conta->recuperaNomeTitular());
    }

    /**
     * @dataProvider geraCpf
     * @covers Titular::__construct()
     * @covers Titular::validaNomeTitular
     * @uses CPF::__construct()
     * @uses Titular::validaNomeTitular()
     */
    public function testRecusaTitularComNomeInvalido($cpf)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Nome precisa ter pelo menos 5 caracteres");

        $titular = new Titular($cpf, "Ana");
    }

    /**
     * @dataProvider geraTitular
     * @covers Titular::recuperaCpf
     * @covers CPF::recuperaNumero
     * @covers Titular::__construct
     * @covers Titular::validaNomeTitular
     * @covers CPF::__construct
     */
    public function testCpfDoTitularPrecisaSerValido(Titular $titular)
    {
        $titularTeste = new Titular(new Cpf('123.456.789-10'), 'Ana paula');
        static::assertEquals($titular->recuperaCpf(), $titularTeste->recuperaCpf());
    }

    /**
     * @dataProvider geraTitular
     * @covers Conta::__construct
     * @covers Conta::recuperaCpfTitular
     * @covers Conta::recuperaNomeTitular
     * @covers Conta::recuperaSaldo
     * @covers CPF::recuperaNumero
     * @covers Titular::recuperaCpf
     * @covers Titular::recuperaNome
     * @uses Conta::__destruct
     */
    public function testCriaContaValida(Titular $titular)
    {
        $conta = new Conta($titular);

        static::assertEquals("João da Silva Sauro", $conta->recuperaNomeTitular());
        static::assertEquals("123.456.789-10", $conta->recuperaCpfTitular());
        static::assertEquals(0, $conta->recuperaSaldo());
    }


    /**
     * @covers CPF::__construct()
     */
    public function testRecusaCpfInvalido()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Cpf inválido");

        $cpf = new CPF("606.678.21-23");
    }

    /**
     * @dataProvider geraContaVazia
     * @covers Conta::deposita
     * @covers Conta::recuperaSaldo
     */
    public function testDepositoValido($conta)
    {
        $conta->deposita(150);

        static::assertEquals(150, $conta->recuperaSaldo());
    }

    /**
     * @dataProvider geraContaVazia
     * @covers Conta::deposita
     */
    public function testDepositoInvalido($conta)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Valor precisa ser positivo");

        $conta->deposita(-100);
    }

    /**
     * @dataProvider geraContaComSaldo
     * @covers Conta::saca
     * @covers Conta::recuperaSaldo
     */
    public function testSaqueValido($conta)
    {
        $conta->saca(200);

        static::assertEquals(800, $conta->recuperaSaldo());
    }

    /**
     * @dataProvider geraContaVazia
     * @covers Conta::saca
     */
    public function testSaqueInvalido($conta)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Saldo indisponível");

        $conta->saca(200);
    }

    /**
     * @dataProvider geraContasTransferenciaComSaldo
     * @covers Conta::transfere
     * @covers Conta::recuperaSaldo
     * @covers Conta::saca
     * @covers Conta::deposita
     */
    public function testTransfereComSaldoPositivo(Conta $conta1, Conta $conta2)
    {
        $conta1->transfere(200, $conta2);

        static::assertEquals(800, $conta1->recuperaSaldo());
        static::assertEquals(200, $conta2->recuperaSaldo());
    }

    /**
     * @dataProvider geraContasTransferenciaSemSaldo
     * @covers Conta::transfere
     */
    public function testTransfereSemSaldo($conta1, $conta2)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Saldo indisponível");
        $conta1->transfere(200, $conta2);
    }


    /**
     * @codeCoverageIgnore
     */
    public function geraContaVazia()
    {
        $cpf = new CPF("123.456.789-10");
        $titular = new Titular($cpf, "João da Silva Sauro");
        $conta = new Conta($titular);

        return [
            [$conta]
        ];
    }

    /**
     * @codeCoverageIgnore
     */
    public function geraContaComSaldo()
    {
        $cpf = new CPF("123.456.789-10");
        $titular = new Titular($cpf, "João da Silva Sauro");
        $conta = new Conta($titular);
        $conta->deposita(1000);

        return [
            [$conta]
        ];
    }

    /**
     * @codeCoverageIgnore
     */
    public function geraContasTransferenciaComSaldo()
    {
        $cpf = new CPF("123.456.789-10");
        $titular = new Titular($cpf, "João da Silva Sauro");
        $conta1 = new Conta($titular);
        $conta1->deposita(1000);

        $cpf = new CPF("987.654.321-10");
        $titular = new Titular($cpf, "Maria");
        $conta2 = new Conta($titular);

        return [
            [$conta1, $conta2]
        ];
    }

    /**
     * @codeCoverageIgnore
     */
    public function geraContasTransferenciaSemSaldo()
    {
        $cpf = new CPF("123.456.789-10");
        $titular = new Titular($cpf, "João da Silva Sauro");
        $conta1 = new Conta($titular);

        $cpf = new CPF("987.654.321-10");
        $titular = new Titular($cpf, "Maria");
        $conta2 = new Conta($titular);

        return [
            [$conta1, $conta2]
        ];
    }

    /**
     * @codeCoverageIgnore
     */
    public function geraTitular()
    {
        $cpf = new CPF("123.456.789-10");
        $titular = new Titular($cpf, "João da Silva Sauro");

        return [
            [$titular]
        ];
    }

    /**
     * @codeCoverageIgnore
     */
    public function geraCpf()
    {
        $cpf = new CPF("123.456.789-10");

        return [
            [$cpf]
        ];
    }
}