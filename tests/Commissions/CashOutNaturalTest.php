<?php

declare(strict_types=1);

namespace Commission\Tests\Commissions;

use Commission\Commissions\CashOutNatural;
use PHPUnit\Framework\TestCase;

class CashOutNaturalTest extends TestCase
{

    /**
     * @param array $csvRow
     * @param array $transfersThisWeek
     * @param float $expectation
     * @dataProvider dataProviderDataForCalculate
     */
    public function testGetFeeAmount($csvRow, $transfersThisWeek, $expectation)
    {
        $cashIn = new CashOutNatural($csvRow, $transfersThisWeek);
        $result = $cashIn->getFeeAmount();

        $this->assertEquals($expectation, $result);
    }

    public function dataProviderDataForCalculate(): array
    {
        return array(
            'transaction in EUR without tax without another transactions this week' => array(
                array('2016-01-05', '2', 'legal', 'cash_out', '300.00', 'EUR'),
                array(),
                '0.00'
            ),
            'transaction in EUR with partial tax' => array(
                array('2016-01-10', '2', 'legal', 'cash_out', '1200.00', 'EUR'),
                array(),
                '0.60'
            ),
            'transaction in USD without tax' => array(
                array('2016-01-05', '2', 'legal', 'cash_out', '400.00', 'USD'),
                array(),
                '0.00'
            ),
            'transaction in USD with partial tax' => array(
                array('2016-01-10', '2', 'legal', 'cash_out', '1400.00', 'USD'),
                array(),
                '0.76'
            ),
            'transaction in JPY without tax' => array(
                array('2016-01-05', '2', 'legal', 'cash_out', '2000', 'JPY'),
                array(),
                '0'
            ),
            'transaction in JPY with partial tax' => array(
                array('2016-01-10', '2', 'legal', 'cash_out', '1000000', 'JPY'),
                array(),
                '2612'
            ),
            'transaction without tax for second transaction this week' => array(
                array('2016-01-05', '2', 'legal', 'cash_out', '300.00', 'EUR'),
                array('EUR' => array('100.00')),
                '0.00'
            ),
            'transaction with partial tax for second transaction this week' => array(
                array('2016-01-05', '2', 'legal', 'cash_out', '1000.00', 'EUR'),
                array('EUR' => array('200.00')),
                '0.60'
            ),
            'transaction with full tax for second transaction this week' => array(
                array('2016-01-05', '2', 'legal', 'cash_out', '200.00', 'EUR'),
                array('EUR' => array('1000.00')),
                '0.60'
            ),
            'transaction with full tax for third transaction this week' => array(
                array('2016-01-05', '2', 'legal', 'cash_out', '200.00', 'EUR'),
                array('EUR' => array('800.00'), 'USD' => array('500.00')),
                '0.60'
            ),
            'transaction with full tax for fourth transaction this week' => array(
                array('2016-01-05', '2', 'legal', 'cash_out', '200.00', 'EUR'),
                array('EUR' => array('100.00', '100.00'), 'USD' => array('100.00')),
                '0.60'
            )
        );
    }
}