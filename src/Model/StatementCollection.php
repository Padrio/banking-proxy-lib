<?php

namespace Padrio\BankingProxy\Model;

use Fhp\Model\StatementOfAccount\StatementOfAccount as FhpStatementCollection;
use Fhp\Model\StatementOfAccount\Statement as FhpStatement;

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
final class StatementCollection
{
    /**
     * @var Statement[]
     */
    public $statements = [];

    /**
     * @param FhpStatementCollection $statementOfAccount
     *
     * @return StatementCollection
     */
    public static function createFromFhpStatement(FhpStatementCollection $statementOfAccount)
    {
        $instance = new self();
        $instance->statements = array_map(function(FhpStatement $statement) {
            return Statement::createFromFinTsStatement($statement);
        }, $statementOfAccount->getStatements());

        return $instance;
    }

    /**
     * @param array $data
     *
     * @return StatementCollection
     */
    public static function createFromArray(array $data)
    {
        $instance = new self();
        $instance->statements = array_map(function(array $statement) {
            return Statement::createFromArray($statement);
        }, $data['statements']);

        return $instance;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $statements = array_map(function(Statement $statement){
            return $statement->toArray();
        }, $this->statements);

        return [
            'statements' => $statements,
        ];
    }
}