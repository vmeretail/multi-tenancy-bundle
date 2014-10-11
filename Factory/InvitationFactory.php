<?php

namespace Tahoe\Bundle\MultiTenancyBundle\Factory;

use Tahoe\Bundle\MultiTenancyBundle\Entity\Invitation;

class InvitationFactory
{
    public function createNew()
    {
        $organizationUser = new Invitation();

        return $organizationUser;
    }
}
