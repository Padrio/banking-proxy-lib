<?php

namespace Padrio\BankingProxy\Model;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Fhp\Model\StatementOfAccount\Transaction as FhpTransaction;

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
final class Transaction
{
    const TYPE_CREDIT = 'credit';
    const TYPE_DEBIT = 'debit';

    /**
     * @var DateTimeImmutable
     */
    public $bookingDate;

    /**
     * @var DateTimeImmutable
     */
    public $valueDate;

    /**
     * @var float
     */
    public $amount;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $bookingText;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $bankCode;

    /**
     * @var string
     */
    public $accountNumber;

    /**
     * @var string
     */
    public $name;

    /**
     * Since the bank does not provide unique identifiers we just has some stuff
     *
     * @return string
     */
    public function getHash()
    {
        return md5(
            $this->name
            . $this->bookingText
            . $this->description
            . $this->amount
        );
    }

    /**
     * @param FhpTransaction $transaction
     *
     * @return Transaction
     */
    public static function createFromFhpTransaction(FhpTransaction $transaction)
    {
        $instance = new self();
        $instance->bookingDate = DateTimeImmutable::createFromMutable($transaction->getBookingDate());
        $instance->valueDate = DateTimeImmutable::createFromMutable($transaction->getValutaDate());
        $instance->amount = $transaction->getAmount();
        $instance->type = $transaction->getCreditDebit();
        $instance->bookingText = $transaction->getBookingText();
        $instance->description = $transaction->getDescription1() . PHP_EOL . $transaction->getDescription2();
        $instance->bankCode = $transaction->getBankCode();
        $instance->accountNumber = $transaction->getAccountNumber();
        $instance->name = $transaction->getName();
        return $instance;
    }

    /**
     * @param array $data
     *
     * @return Transaction
     */
    public static function createFromArray(array $data)
    {
        $instance = new self();
        $instance->bookingDate = DateTimeImmutable::createFromFormat(DateTime::ISO8601, $data['bookingDate']);
        $instance->valueDate = DateTimeImmutable::createFromFormat(DateTime::ISO8601, $data['valueDate']);
        $instance->amount = $data['amount'];
        $instance->type = $data['type'];
        $instance->bookingText = $data['bookingText'];
        $instance->description = $data['description'];
        $instance->bankCode = $data['bankCode'];
        $instance->accountNumber = $data['accountNumber'];
        $instance->name = $data['name'];
        return $instance;
    }

    /**
     * @param bool $includeHash
     *
     * @return array
     */
    public function toArray($includeHash = false)
    {
        $payload = [
            'bookingDate' => $this->bookingDate->format(DateTimeInterface::ISO8601),
            'valueDate' => $this->valueDate->format(DateTimeInterface::ISO8601),
            'amount' => $this->amount,
            'type' => $this->type,
            'bookingText' => $this->bookingText,
            'description' => $this->description,
            'bankCode' => $this->bankCode,
            'accountNumber' => $this->accountNumber,
            'name' => $this->name,
        ];

        if($includeHash === true) {
            $payload['hash'] = $this->getHash();
        }

        return $payload;
    }
}