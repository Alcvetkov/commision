<?php

declare(strict_types=1);

namespace Commission\Tests\Commissions;

use Commission\Commissions\CashIn;
use PHPUnit\Framework\TestCase;

class CashInTest extends TestCase
{

    /**
     * @param array $csvRow
     * @param float $expectation
     * @dataProvider dataProviderDataForCalculate
     */
    public function testGetFeeAmount($csvRow, $expectation)
    {
        $cashIn = new CashIn($csvRow);
        $result = $cashIn->getFeeAmount();

        $this->assertEquals($expectation, $result);
    }

    public function dataProviderDataForCalculate(): array
    {
        return array(
            'transaction in EUR without max fee tax' => array(
                array('2016-01-05', '2', 'natural', 'cash_in', '200.00', 'EUR'),
                '0.06'
            ),
            'transaction in EUR with max fee tax' => array(
                array('2016-01-10', '2', 'legal', 'cash_in', '1000000.00', 'EUR'),
                '5.00'
            ),
            'transaction in USD without max fee tax' => array(
                array('2016-01-05', '2', 'natural', 'cash_in', '200.00', 'USD'),
                '0.06'
            ),
            'transaction in USD with max fee tax' => array(
                array('2016-01-10', '2', 'legal', 'cash_in', '1000000.00', 'USD'),
                '5.74'
            ),
            'transaction in JPY without max fee tax' => array(
                array('2016-01-05', '2', 'natural', 'cash_in', '200000', 'JPY'),
                '60'
            ),
            'transaction in JPY with max fee tax' => array(
                array('2016-01-10', '2', 'legal', 'cash_in', '100000000', 'JPY'),
                '647'
            )
        );
    }
}