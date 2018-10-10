<?php

namespace Padrio\BankingProxy\Model;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Fhp\Model\StatementOfAccount\Statement as FhpStatement;

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
final class Statement
{
    const TYPE_CREDIT = 'credit';
    const TYPE_DEBIT = 'debit';

    /**
     * @var Transaction[]
     */
    public $transactions = [];

    /**
     * @var Balance
     */
    public $balance;

    /**
     * @var string
     */
    public $type;

    /**
     * @var DateTimeImmutable
     */
    public $date;

    /**
     * @param FhpStatement $statement
     *
     * @return Statement
     */
    public static function createFromFinTsStatement(FhpStatement $statement)
    {
        $instance = new self();
        $instance->balance = Balance::createFromFhpStatement($statement);
        $instance->date = DateTimeImmutable::createFromMutable($statement->getDate());
        $instance->type = $statement->getCreditDebit();

        foreach($statement->getTransactions() as $transaction) {
            $instance->transactions[] = Transaction::createFromFhpTransaction($transaction);
        }

        return $instance;
    }

    /**
     * @param array $data
     *
     * @return Statement
     */
    public static function createFromArray(array $data)
    {
        $instance = new self();
        $instance->balance = Balance::createFromArray($data['balance']);
        $instance->date = DateTimeImmutable::createFromFormat(DateTime::ISO8601, $data['date']);
        $instance->type = $data['type'];

        foreach($data['transactions'] as $transaction) {
            $instance->transactions[] = Transaction::createFromArray($transaction);
        }

        return $instance;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $transactions = array_map(function(Transaction $transaction){
            return $transaction->toArray();
        }, $this->transactions);

        return [
            'transactions' => $transactions,
            'balance' => $this->balance->toArray(),
            'type' => $this->type,
            'date' => $this->date->format(DateTimeInterface::ISO8601),
        ];
    }
}