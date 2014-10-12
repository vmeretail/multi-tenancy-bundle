<?php


namespace Tahoe\Bundle\MultiTenancyBundle\Model;


interface InvitationInterface
{
    /**
     * @return MultiTenantTenantInterface
     */
    public function getTenant();

    /**
     * @return string
     */
    public function getEmail();
}
