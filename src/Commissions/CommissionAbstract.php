<?php

declare(strict_types=1);

namespace Commission\Commissions;

use Commission\Factory;

abstract class CommissionAbstract
{
    const CURRENCY_STRING_EUR = 'EUR';
    const CURRENCY_STRING_USD = 'USD';
    const CURRENCY_STRING_JPY = 'JPY';

    protected $date;

    protected $clientID;

    protected $cashAmount;

    protected $currency;

    protected $feeAmount;

    protected $scale;

    public function __construct($csvRow)
    {
        $this->setDate($csvRow[Factory::DATE_FIELD_NUMBER]);
        $this->setClientID($csvRow[Factory::CLIENT_ID_FIELD_NUMBER]);
        $this->setCashAmount($csvRow[Factory::AMOUNT_FIELD_NUMBER]);
        $this->setCurrency($csvRow[Factory::CURRENCY_FIELD_NUMBER]);
        $this->setScale();
        $this->calculateFee();
    }

    /**
     * Set the date of the transaction.
     *
     * @param mixed $date the date of transaction
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Set the client id.
     *
     * @param mixed $clientID the client id
     */
    public function setClientID($clientID)
    {
        $this->clientID = $clientID;
    }

    /**
     * Set the cash amount.
     *
     * @param mixed $cashAmount the cash amount
     */
    public function setCashAmount($cashAmount)
    {
        $this->cashAmount = $cashAmount;
    }

    /**
     * Set the currency of the transaction.
     *
     * @param mixed $currency the currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * Get the fee amount.
     *
     * @return mixed return the fee amount
     */
    public function getFeeAmount()
    {
        return $this->feeAmount;
    }

    /** Set the scale based of currency. */
    public function setScale()
    {
        if ($this->currency === static::CURRENCY_STRING_JPY)
        {
            $this->scale = Factory::JAPANESE_CURRENCY_SCALE;
        }
        else
        {
            $this->scale = Factory::DEFAULT_CURRENCY_SCALE;
        }
    }

    /**
     * Round up an amount with needed precision.
     *
     * @param string $amount the amount value
     * @param int $precision the precision
     *
     * @return string return the round up value
     */
    public function roundUp($amount, $precision) :string
    {
        $precision = (int) $precision;
        if ($precision < 0) {
            $precision = 0;
        }
        $decPointPosition = strpos($amount, '.');
        if ($decPointPosition === false) {
            return$amount;
        }
        $floorValue = (float) (substr($amount, 0, $decPointPosition + $precision + 1));
        $followingDecimals = (int) substr($amount, $decPointPosition + $precision + 1);
        if ($followingDecimals) {
            $ceilValue = $floorValue + pow(10, -$precision);
        }
        else {
            $ceilValue = $floorValue;
        }

        return(string) $ceilValue;
    }

    /** Calculate the fee amount. */
    abstract protected function calculateFee();
}
