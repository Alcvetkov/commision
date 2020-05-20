<?php

declare(strict_types=1);

namespace Commission\Tests\Commissions;

use Commission\Commissions\CashOutLegal;
use PHPUnit\Framework\TestCase;

class CashOutLegalTest extends TestCase
{
    /**
     * @param $csvRow
     * @param $expectation
     * @dataProvider dataProviderDataForCalculate
     */
    public function testGetFeeAmount($csvRow, $expectation)
    {
        $cashIn = new CashOutLegal($csvRow);
        $result = $cashIn->getFeeAmount();

        $this->assertEquals($expectation, $result);
    }

    public function dataProviderDataForCalculate(): array
    {
        return array(
            'transaction in EUR without min fee tax' => array(
                array('2016-01-05', '2', 'legal', 'cash_out', '300.00', 'EUR'),
                '0.90'
            ),
            'transaction in EUR with min fee tax' => array(
                array('2016-01-10', '2', 'legal', 'cash_out', '50.00', 'EUR'),
                '0.50'
            ),
            'transaction in USD without min fee tax' => array(
                array('2016-01-05', '2', 'legal', 'cash_out', '400.00', 'USD'),
                '1.20'
            ),
            'transaction in USD with min fee tax' => array(
                array('2016-01-10', '2', 'legal', 'cash_out', '10.00', 'USD'),
                '0.57'
            ),
            'transaction in JPY without min fee tax' => array(
                array('2016-01-05', '2', 'legal', 'cash_out', '2000000', 'JPY'),
                '6000'
            ),
            'transaction in JPY with min fee tax' => array(
                array('2016-01-10', '2', 'legal', 'cash_out', '100', 'JPY'),
                '64'
            )
        );
    }
}