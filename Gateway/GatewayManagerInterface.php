<?php

namespace Tahoe\Bundle\MultiTenancyBundle\Gateway;

use Tahoe\Bundle\MultiTenancyBundle\Entity\Tenant;

interface GatewayManagerInterface
{
    public function createAccount(Tenant $tenant);

    public function createSubscription(Tenant $tenant);
}