<?php

namespace Tahoe\Bundle\MultiTenancyBundle\Factory;

use Tahoe\Bundle\MultiTenancyBundle\Entity\OrganizationUser;

class OrganizationUserFactory
{
    public function createNew()
    {
        $organizationUser = new OrganizationUser();

        return $organizationUser;
    }
}
