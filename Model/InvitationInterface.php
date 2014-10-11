<?php


namespace Tahoe\Bundle\MultiTenancyBundle\Model;


interface InvitationInterface
{
    /**
     * @return MultiTenantOrganizationInterface
     */
    public function getOrganization();

    /**
     * @return string
     */
    public function getEmail();
}
