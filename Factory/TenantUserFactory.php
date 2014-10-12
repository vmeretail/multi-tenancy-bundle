<?php

namespace Tahoe\Bundle\MultiTenancyBundle\Factory;

use Tahoe\Bundle\MultiTenancyBundle\Entity\TenantUser;

class TenantUserFactory
{
    public function createNew()
    {
        $tenantUser = new TenantUser();

        return $tenantUser;
    }
}
