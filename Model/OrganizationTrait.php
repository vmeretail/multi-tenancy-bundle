<?php

namespace Tahoe\Bundle\MultiTenancyBundle\Model;

/**
 * Trait OrganizationTrait
 *
 * @author Konrad Podgórski <konrad.podgorski@gmail.com>
 */
trait OrganizationTrait
{
    /**
     * @var MultiTenantOrganizationInterface
     */
    protected $organization;

    /**
     * @return MultiTenantOrganizationInterface
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param MultiTenantOrganizationInterface $organization
     *
     * @return $this
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;

        return $this;
    }
}
