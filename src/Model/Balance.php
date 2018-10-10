<?php

namespace Padrio\BankingProxy\Model;

use Fhp\Model\StatementOfAccount\Statement as FhpStatement;

/**
 * This class will be used in future to unify the balance in cent because floats sucks
 * I've created it to make the change without BC breaks.
 *
 * @author Pascal Krason <p.krason@padr.io>
 */
final class Balance
{
    /**
     * @var float
     */
    public $start = 0.0;

    public function __construct($start = 0.0)
    {
        $this->start = $start;
    }

    /**
     * @param FhpStatement $statement
     *
     * @return Balance
     */
    public static function createFromFhpStatement(FhpStatement $statement)
    {
        return new self($statement->getStartBalance());
    }

    /**
     * @param array $data
     *
     * @return Balance
     */
    public static function createFromArray(array $data)
    {
        return new self(
            $data['start']
        );
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'start' => $this->start,
        ];
    }
}