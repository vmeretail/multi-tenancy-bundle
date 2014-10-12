<?php
namespace Tahoe\Bundle\MultiTenancyBundle\Gateway;

use Tahoe\Bundle\MultiTenancyBundle\Entity\Tenant;

class RecurlyManager implements GatewayManagerInterface
{
    private $recurly;
    private $accountPrefix;
    private $planName;

    public function __construct($subdomain, $privateKey, $prefix, $plan_name)
    {
        \Recurly_Client::$subdomain = $subdomain;
        \Recurly_Client::$apiKey = $privateKey;
        $this->accountPrefix = $prefix;
        $this->planName = $plan_name;
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

    public function createSubscription(Tenant $tenant, array $data)
    {
        $account_code = sprintf("%s-%s", $this->accountPrefix, $tenant->getId());

        $account = \Recurly_Account::get($account_code);
        $account->first_name = $data['first_name'];
        $account->last_name = $data['last_name'];

        $subscription = new \Recurly_Subscription();
        $subscription->plan_code = $this->getPlanName();
        $subscription->currency = 'GBP'; // TODO: make this more flexible

        $billing_info = new \Recurly_BillingInfo();
        $billing_info->number = $data['credit_card_number'];
        $billing_info->month = $data['month'];
        $billing_info->year = $data['year'];

        $account->billing_info = $billing_info;
        $subscription->account = $account;

        $subscription->create();
    }

    /**
     * @return mixed
     */
    public function getPlanName()
    {
        return $this->planName;
    }
}
