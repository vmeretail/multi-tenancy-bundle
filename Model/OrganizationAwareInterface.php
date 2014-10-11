<?php


namespace Tahoe\Bundle\MultiTenancyBundle\Model;

/**
 * Class OrganizationAwareInterface
 *
 * @author Konrad Podgórski <konrad.podgorski@gmail.com>
 */
interface OrganizationAwareInterface
{
    /**
     * @return MultiTenantOrganizationInterface
     */
    public function getOrganization();

    /**
     * @param MultiTenantOrganizationInterface $organization
     *
     * @return $this
     */
    public function setOrganization($organization);
}
