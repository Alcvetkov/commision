<?php

declare(strict_types=1);

namespace Commission\Tests;

use Commission\Factory;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{

    /** @var Factory */
    private $factory;

    public function setUp()
    {
        $this->factory = new Factory();
    }

    /**
     * @param array $csvRow
     * @param array $naturalCashOutTransactions
     * @param array $expectation
     *
     * @dataProvider dataProviderForGetTransactionsThisWeek
     */
    public function testGetTransactionsThisWeek($csvRow, $naturalCashOutTransactions, $expectation)
    {
        $this->assertEquals($expectation, $this->factory->getTransactionsThisWeek($csvRow, $naturalCashOutTransactions));
    }

    public function dataProviderForGetTransactionsThisWeek()
    {
        return array(
            'no cash out this week' => array(
                array('2016-01-05', '2', 'natural', 'cash_in', '200.00', 'EUR'),
                array(),
                array()
            ),
            'no cash out this week for current client' => array(
                array('2016-01-05', '2', 'natural', 'cash_in', '200.00', 'EUR'),
                array('6' => array(array('2016-01-04', '2', 'natural', 'cash_in', '200.00', 'EUR'))),
                array()
            ),
            'one cash out this week for current client' => array(
                array('2016-01-05', '2', 'natural', 'cash_in', '200.00', 'EUR'),
                array('2' => array(array('2016-01-04', '2', 'natural', 'cash_in', '100.00', 'EUR'))),
                array('EUR' => array('100.00')
                )
            ),
            'two cash outs this week for current client' => array(
                array('2016-01-06', '2', 'natural', 'cash_in', '200.00', 'EUR'),
                array('2' => array(array('2016-01-04', '2', 'natural', 'cash_in', '100.00', 'EUR'), array('2016-01-05', '2', 'natural', 'cash_in', '100.00', 'EUR'))),
                array('EUR' => array('100.00', '100.00'))
            )
        );
    }
}