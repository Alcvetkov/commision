<?php

declare(strict_types=1);

namespace Commission\Tests\Commissions;

use Commission\Commissions\CashIn;
use PHPUnit\Framework\TestCase;

class CommissionAbstractTest extends TestCase
{

    /** @var CashIn */
    private $commission;

    public function setUp()
    {
        $this->commission = new CashIn(array('2016-01-05', '2', 'natural', 'cash_in', '200.00', 'EUR'));
    }

    /**
     * @param float $amount
     * @param int $precision
     * @param float $expectation
     *
     * @dataProvider dataProviderForRoundUp
     */
    public function testRoundUp($amount, $precision, $expectation)
    {
        $this->assertEquals($expectation, $this->commission->roundUp($amount, $precision));
    }

    public function dataProviderForRoundUp()
    {
        return array(
            'round up with precision 2' => array(
                '0.456',
                '2',
                '0.46'
            ),
            'round up with precision 0' => array(
                '357.13',
                '0',
                '358.0'
            )
        );
    }
}