<?php

declare(strict_types=1);

namespace Commission\Commissions;

use Commission\Factory;

class CashOutLegal extends CommissionAbstract
{
    /** {@inheritdoc} */
    protected function calculateFee()
    {
        $calculatedFeeAmount = $this->roundUp(
            (string) ($this->cashAmount * (Factory::CASH_OUT_LEGAL_FEE_PERCENT / 100)),
            $this->scale
        );
        $minimumFeeAmount = $this->getMinimumFeeAmount();
        if ($calculatedFeeAmount < $minimumFeeAmount) {
            $calculatedFeeAmount = $minimumFeeAmount;
        }
        $this->feeAmount = $calculatedFeeAmount;
    }

    /**
     * Get the minimum fee amount in the current currency.
     *
     * @return string Return the minimum fee amount
     */
    protected function getMinimumFeeAmount():string
    {
        switch ($this->currency) {
            case static::CURRENCY_STRING_EUR:

                return Factory::CASH_OUT_LEGAL_FEE_MINIMUM_AMOUNT;
            case static::CURRENCY_STRING_USD:

                return bcmul(
                    Factory::CASH_OUT_LEGAL_FEE_MINIMUM_AMOUNT,
                    Factory::CONVERSION_RATE_EUR_TO_USD, $this->scale
                );
            case static::CURRENCY_STRING_JPY:

                return bcmul(
                    Factory::CASH_OUT_LEGAL_FEE_MINIMUM_AMOUNT,
                    Factory::CONVERSION_RATE_EUR_TO_JPY, $this->scale
                );
        }
    }
}
