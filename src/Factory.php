<?php

declare(strict_types=1);

namespace Commission;

use Commission\Commissions\CashIn;
use Commission\Commissions\CashOutLegal;
use Commission\Commissions\CashOutNatural;
use DateTime;

class Factory
{
    const CSV_FIELD_DELIMITER = ',';
    const DATE_FIELD_NUMBER = 0;
    const CLIENT_ID_FIELD_NUMBER = 1;
    const PERSON_TYPE_FIELD_NUMBER = 2;
    const COMMISSION_TYPE_FIELD_NUMBER = 3;
    const AMOUNT_FIELD_NUMBER = 4;
    const CURRENCY_FIELD_NUMBER = 5;
    const DEFAULT_CURRENCY_SCALE = 2;
    const JAPANESE_CURRENCY_SCALE = 0;
    const CASH_IN_FEE_PERCENT = 0.03;
    const CASH_IN_FEE_MAXIMUM_AMOUNT = '5.00';
    const CASH_OUT_NATURAL_FEE_DEFAULT_PERCENT = 0.3;
    const CASH_OUT_NATURAL_FEE_FREE_OF_CHARGE_AMOUNT = '1000.00';
    const CASH_OUT_NATURAL_FEE_FREE_TRANSACTION_NUMBERS_PER_WEEK = 3;
    const CASH_OUT_LEGAL_FEE_PERCENT = 0.3;
    const CASH_OUT_LEGAL_FEE_MINIMUM_AMOUNT = '0.5';
    const CONVERSION_RATE_EUR_TO_USD = '1.1497';
    const CONVERSION_RATE_EUR_TO_JPY = '129.53';

    /**
     * Process the input file.
     *
     * @param string $filename the name of the file
     *
     * @throws \Exception
     */
    public function processFile($filename)
    {
        $fileHandle = fopen($filename, 'r');
        if ($fileHandle === false) {
            echo 'Could not open the file.'.PHP_EOL;

            return;
        }
        $naturalCashOutTransactions[] = [];
        while ($row = fgetcsv($fileHandle, 0, static::CSV_FIELD_DELIMITER)) {
            if ($row[static::COMMISSION_TYPE_FIELD_NUMBER] === 'cash_in') {
                $commissionObject = new CashIn($row);
            }
            elseif ($row[static::PERSON_TYPE_FIELD_NUMBER] === 'legal') {
                $commissionObject = new CashOutLegal($row);
            }
            else {
                $commissionObject = new CashOutNatural($row, $this->getTransactionsThisWeek($row, $naturalCashOutTransactions));
                $naturalCashOutTransactions[$row[static::CLIENT_ID_FIELD_NUMBER]][] = $row;
            }
            echo $commissionObject->getFeeAmount().PHP_EOL;
        }
        fclose($fileHandle);
    }

    /**
     * Get the transactions of current week.
     *
     * @param array $row all transaction information
     * @param array $naturalCashOutTransactions already processed natural cash outs
     *
     * @return array return the transaction of current week
     *
     * @throws \Exception
     */
    public function getTransactionsThisWeek(array $row, array $naturalCashOutTransactions):array
    {
        if (empty($naturalCashOutTransactions[$row[static::CLIENT_ID_FIELD_NUMBER]])) {

            return[];
        }
        $transactionsThisWeek = [];
        $currentDate = new DateTime($row[static::DATE_FIELD_NUMBER]);
        $currentWeek = $currentDate->format('oW');
        foreach ($naturalCashOutTransactions[$row[static::CLIENT_ID_FIELD_NUMBER]] as $transaction) {
            $transactionDate = new DateTime($transaction[static::DATE_FIELD_NUMBER]);
            if ($currentWeek !== $transactionDate->format('oW')) {

                continue;
            }
            $transactionsThisWeek[$transaction[static::CURRENCY_FIELD_NUMBER]][] = $transaction[static::AMOUNT_FIELD_NUMBER];
        }

        return$transactionsThisWeek;
    }
}
