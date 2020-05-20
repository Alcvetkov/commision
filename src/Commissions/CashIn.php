<?php

declare(strict_types=1);

namespace Commission\Commissions;

use Commission\Factory;

class CashIn extends CommissionAbstract
{
    /** {@inheritdoc} */
    protected function calculateFee()
    {
        $calculatedFeeAmount = $this->roundUp(
            (string) ($this->cashAmount * (Factory::CASH_IN_FEE_PERCENT / 100)),
            $this->scale
        );
        $maximumFeeAmount = $this->getMaximumFeeAmount();
        if ($calculatedFeeAmount > $maximumFeeAmount){
            $calculatedFeeAmount = $maximumFeeAmount;
        }
        $this->feeAmount = $calculatedFeeAmount;
    }

    /**
     * Get the maximum fee amount in the current currency.
     *
     * @return string return the maximum fee amount
     */
    protected function getMaximumFeeAmount():string
    {
        switch ($this->currency){
            case static::CURRENCY_STRING_EUR:

                return Factory::CASH_IN_FEE_MAXIMUM_AMOUNT;
            case static::CURRENCY_STRING_USD:

                return bcmul(
                    Factory::CASH_IN_FEE_MAXIMUM_AMOUNT,
                    Factory::CONVERSION_RATE_EUR_TO_USD, $this->scale
                );
            case static::CURRENCY_STRING_JPY:

                return bcmul(
                    Factory::CASH_IN_FEE_MAXIMUM_AMOUNT,
                    Factory::CONVERSION_RATE_EUR_TO_JPY, $this->scale
                );
        }
    }
}
