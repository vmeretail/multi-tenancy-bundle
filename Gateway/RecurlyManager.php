<?php
namespace Tahoe\Bundle\MultiTenancyBundle\Gateway;

use Tahoe\Bundle\MultiTenancyBundle\Entity\Tenant;

class RecurlyManager implements GatewayManagerInterface
{
    private $recurly;
    private $accountPrefix;

    public function __construct($subdomain, $privateKey, $prefix)
    {
        \Recurly_Client::$subdomain = $subdomain;
        \Recurly_Client::$apiKey = $privateKey;
        $this->accountPrefix = $prefix;
    }

    /**
     * @return mixed
     */
    public function getAccountPrefix()
    {
        return $this->accountPrefix;
    }

    public function createAccount(Tenant $tenant)
    {
        $account_code = sprintf("%s-%s", $this->accountPrefix, $tenant->getId());
        $account = new \Recurly_Account($account_code);
        $account->company_name = $tenant->getName();
        $account->create();
    }

    public function createSubscription(Tenant $tenant)
    {
        $account_code = sprintf("%s-%s", $this->accountPrefix, $tenant->getId());
        $account = \Recurly_Account::get($account_code);
    }
} 