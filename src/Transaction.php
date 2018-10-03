<?php

declare(strict_types=1);

namespace Padrio\BankingProxy;

use DateTime;
use Exception;
use Fhp\Adapter\Exception\AdapterException;
use Fhp\Adapter\Exception\CurlException;
use Fhp\FinTs;
use Fhp\Model\SEPAAccount;
use Padrio\BankingProxy\Shared\Model\StatementCollection;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
final class Transaction
{
    /**
     * @var FinTs
     */
    private $finTs;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(FinTs $finTs, LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger();
        $this->finTs = $finTs;
    }

    /**
     * @param int      $accountNumber
     * @param DateTime $from
     * @param DateTime $to
     *
     * @return StatementCollection
     * @throws Exception
     */
    public function getStatementCollection(int $accountNumber, DateTime $from, DateTime $to): StatementCollection
    {
        $account = $this->findAccountByNumber($accountNumber);
        if($account === null) {
            throw new Exception('Could not find account '. $accountNumber);
        }

        $statement = $this->finTs->getStatementOfAccount($account, $from, $to);
        return StatementCollection::createFromFhpStatement($statement);
    }

    public function findAccountByNumber(int $accountNumber): ?SEPAAccount
    {
        static $cache = [];
        if(isset($cache[$accountNumber])) {
            return $cache[$accountNumber];
        }

        try {
            $accounts = $this->finTs->getSEPAAccounts();
        } catch (CurlException | AdapterException $e) {
            $this->logger->critical($e->getMessage());

            return null;
        }

        foreach($accounts as $account) {
            if($account->getAccountNumber() == $accountNumber) {
                return $cache[$accountNumber] = $account;
            }
        }

        return null;
    }
}