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

    public function subscriptionExists(Tenant $tenant)
    {
        $subscription = $this->getSubscription($tenant);

        return $subscription !== null;
    }

    public function getSubscription(Tenant $tenant)
    {
        try {
            $account_code = sprintf("%s-%s", $this->accountPrefix, $tenant->getId());
            $subscriptions = \Recurly_SubscriptionList::getForAccount($account_code);
        } catch(\Exception $e)
        {
            return null;
        }

        return $subscriptions->current();
    }

    public function getBillingInfo(Tenant $tenant)
    {
        try {
            $account_code = sprintf("%s-%s", $this->accountPrefix, $tenant->getId());
            $billing = \Recurly_BillingInfo::get($account_code);
        } catch(\Exception $e)
        {
            return null;
        }

        return $billing;
    }

    public function updateBillingInfo(Tenant $tenant, $data)
    {
        $account_code = sprintf("%s-%s", $this->accountPrefix, $tenant->getId());
        $billing_info = $this->createBillingInfo($data);
        $billing_info->account_code = $account_code;
        $billing_info->update();
    }

    public function createSubscription(Tenant $tenant, $data)
    {
        $account_code = sprintf("%s-%s", $this->accountPrefix, $tenant->getId());

        $account = \Recurly_Account::get($account_code);
        $account->first_name = $data->first_name;
        $account->last_name = $data->last_name;

        $subscription = new \Recurly_Subscription();
        $subscription->plan_code = $this->getPlanName();
        $subscription->currency = 'GBP'; // TODO: make this more flexible

        $billing_info = $this->createBillingInfo($data);

        $account->billing_info = $billing_info;
        $subscription->account = $account;

        $subscription->create();
    }

    /**
     * @param $data
     * @return \Recurly_BillingInfo
     */
    private function createBillingInfo($data)
    {
        $billing_info = new \Recurly_BillingInfo();
        $billing_info->number = str_replace(' ', '', $data->credit_card_number);
        $expiration = explode(" / ", $data->expiration);
        $billing_info->month = $expiration[0];
        $billing_info->year = $expiration[1];
        $billing_info->verification_value = $data->verification_value;

        return $billing_info;
    }

    /**
     * @return mixed
     */
    public function getPlanName()
    {
        return $this->planName;
    }
}
