<?php

namespace Z38\IbanTool\Tests;

use Z38\IbanTool\IbanTool;

/**
 * @covers \Z38\IbanTool\IbanTool
 */
class IbanToolTest extends \PHPUnit_Framework_TestCase
{
    protected $tool;

    public function setUp()
    {
        $this->tool = new IbanTool([
            'ibantool_jar' => __DIR__.'/ibantool_java.jar'
        ]);
    }

    /**
     * @dataProvider accountSamples
     */
    public function testConvert($bcpc, $account, $expectedIban)
    {
        $actualIban = $this->tool->convert($bcpc, $account);
        $this->assertEquals($expectedIban, $actualIban);
    }

    /**
     * @dataProvider postalAccountSamples
     */
    public function testConvertPostal($postalAccount, $expectedIban)
    {
        $actualIban = $this->tool->convertPostal($postalAccount);
        $this->assertEquals($expectedIban, $actualIban);
    }

    public function postalAccountSamples()
    {
        return [
            ['80-470-3',    'CH1809000000800004703'],
            ['30-4000-1',   'CH6809000000300040001'],
            ['50-19145-0',  'CH6809000000500191450'],
            ['60-361198-8', 'CH6209000000603611988'],
            ['60-515241-7', 'CH4209000000605152417']
        ];
    }

    public function accountSamples()
    {
        return [
            ['00790',       '41 8.232.303.88', 'CH6400790041823230388'],
            ['80-151-4',    '3525-8.888766.2', 'CH5200700352588887662'],
            ['709',         '1109-0629613',    'CH0500700110900629613'],
            ['ZKBKCHZZ80A', '3509-3.100471.3', 'CH9400700350931004713']
        ];
    }
}
