<?php

declare(strict_types=1);

namespace Commission\Commissions;

use Commission\Factory;

class CashOutNatural extends CommissionAbstract
{
    /** @var array transactions this week */
    protected $transactionsThisWeek = [];

    /**
     * CashOutNatural constructor.
     *
     * @param array $csvRow the current transaction information
     * @param array $transactionsThisWeek array of transactions this week
     */
    public function __construct($csvRow, $transactionsThisWeek)
    {
        $this->setTransactionsThisWeek($transactionsThisWeek);
        parent::__construct($csvRow);
    }

    /**
     * Set the transactions of the current client of this week.
     *
     * @param array $transactionsThisWeek array of transactions this week
     */
    public function setTransactionsThisWeek(array $transactionsThisWeek)
    {
        $this->transactionsThisWeek = $transactionsThisWeek;
    }

    /** {@inheritdoc} */
    protected function calculateFee()
    {
        $amountForCalculate = $this->getAmountForCalculate();
        if ($amountForCalculate <= 0) {
            $this->feeAmount = 0.00;

            return;
        }
        $calculatedFeeAmount = $this->roundUp(
            (string) ($amountForCalculate * (Factory::CASH_OUT_NATURAL_FEE_DEFAULT_PERCENT / 100)),
            $this->scale
        );
        if ($this->currency === static::CURRENCY_STRING_JPY) {
            $calculatedFeeAmount = ceil($calculatedFeeAmount);
        }
        $this->feeAmount = $calculatedFeeAmount;
    }

    /**
     * Get the amount for fee calculate.
     *
     * @return string return the amount for calculate
     */
    protected function getAmountForCalculate():string
    {
        $freeOfChargeAmountForCommissionCurrency = $this->getFreeOfChargeAmount($this->currency);
        if (empty($this->transactionsThisWeek)) {

            return bcsub(
                $this->cashAmount,
                $freeOfChargeAmountForCommissionCurrency,
                $this->scale
            );
        }
        $transactionCounter = 0;
        $transactionsAmountThisWek = '0.00';
        foreach ($this->transactionsThisWeek as $currency => $cashAmounts) {
            $transactionCounter += count($cashAmounts);
            if ($transactionCounter >= Factory::CASH_OUT_NATURAL_FEE_FREE_TRANSACTION_NUMBERS_PER_WEEK) {

                return $this->cashAmount;
            }
            $cashAmountForCurrentCurrency = 0;
            foreach ($cashAmounts as $cashAmount) {
                $cashAmountForCurrentCurrency += $cashAmount;
            }
            $currentAmountInCommissionCurrency = $this->getCashInCurrency(
                $currency,
                (string) $cashAmountForCurrentCurrency
            );
            $transactionsAmountThisWek = bcadd(
                $transactionsAmountThisWek,
                $currentAmountInCommissionCurrency,
                Factory::DEFAULT_CURRENCY_SCALE
            );
            if ($transactionsAmountThisWek >= $freeOfChargeAmountForCommissionCurrency) {

                return $this->cashAmount;
            }
        }
        $discountForCurrentWeek = bcsub(
            $freeOfChargeAmountForCommissionCurrency,
            $transactionsAmountThisWek,
            Factory::DEFAULT_CURRENCY_SCALE
        );

        return bcsub(
            $this->cashAmount,
            $discountForCurrentWeek,
            Factory::DEFAULT_CURRENCY_SCALE
        );
    }

    /**
     * Get the free of charge amount for specific currency.
     *
     * @param string $currency the specific currency
     *
     * @return string return the free of charge amount
     */
    protected function getFreeOfChargeAmount(string $currency):string
    {
        switch ($currency) {
            case static::CURRENCY_STRING_EUR:

                return Factory::CASH_OUT_NATURAL_FEE_FREE_OF_CHARGE_AMOUNT;
            case static::CURRENCY_STRING_USD:

                return bcmul(Factory::CASH_OUT_NATURAL_FEE_FREE_OF_CHARGE_AMOUNT, Factory::CONVERSION_RATE_EUR_TO_USD, Factory::DEFAULT_CURRENCY_SCALE);
            case static::CURRENCY_STRING_JPY:

                return bcmul(Factory::CASH_OUT_NATURAL_FEE_FREE_OF_CHARGE_AMOUNT, Factory::CONVERSION_RATE_EUR_TO_JPY, Factory::DEFAULT_CURRENCY_SCALE);
        }
    }

    /**
     * Convert a cash amount in needed currency.
     *
     * @param string $currency the needed currency
     * @param string $amount the cash amount
     *
     * @return string return the cash in needed currency
     */
    protected function getCashInCurrency(string $currency, string $amount):string
    {
        if ($currency === $this->currency) {

            return $amount;
        }
        if ($this->currency === static::CURRENCY_STRING_EUR) {
            if ($currency === static::CURRENCY_STRING_USD) {

                return bcdiv(
                    $amount,
                    Factory::CONVERSION_RATE_EUR_TO_USD,
                    Factory::DEFAULT_CURRENCY_SCALE
                );
            }
            if ($currency === static::CURRENCY_STRING_JPY) {

                return bcdiv(
                    $amount,
                    Factory::CONVERSION_RATE_EUR_TO_JPY,
                    Factory::DEFAULT_CURRENCY_SCALE
                );
            }
        }
        if ($this->currency === static::CURRENCY_STRING_USD) {
            $amountInEUR = bcmul(
                $amount,
                Factory::CONVERSION_RATE_EUR_TO_USD,
                Factory::DEFAULT_CURRENCY_SCALE
            );
            if ($currency === static::CURRENCY_STRING_EUR) {

                return $amountInEUR;
            }
            if ($currency === static::CURRENCY_STRING_JPY) {

                return bcdiv(
                    $amountInEUR,
                    Factory::CONVERSION_RATE_EUR_TO_JPY,
                    Factory::DEFAULT_CURRENCY_SCALE
                );
            }
        }
        if ($this->currency === static::CURRENCY_STRING_JPY) {
            $amountInEUR = $amountInEUR = bcmul(
                $amount,
                Factory::CONVERSION_RATE_EUR_TO_JPY,
                Factory::DEFAULT_CURRENCY_SCALE
            );
            if ($currency === static::CURRENCY_STRING_EUR) {

                return $amountInEUR;
            }
            if ($currency === static::CURRENCY_STRING_USD) {

                return bcdiv(
                    $amountInEUR,
                    Factory::CONVERSION_RATE_EUR_TO_USD,
                    Factory::DEFAULT_CURRENCY_SCALE
                );
            }
        }
    }
}
