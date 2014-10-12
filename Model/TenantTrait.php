<?php

namespace Tahoe\Bundle\MultiTenancyBundle\Model;

/**
 * Trait TenantTrait
 *
 * @author Konrad Podgórski <konrad.podgorski@gmail.com>
 */
trait TenantTrait
{
    /**
     * @var MultiTenantTenantInterface
     */
    protected $tenant;

    /**
     * @return MultiTenantTenantInterface
     */
    public function getTenant()
    {
        return $this->tenant;
    }

    /**
     * @param MultiTenantTenantInterface $tenant
     *
     * @return $this
     */
    public function setTenant($tenant)
    {
        $this->tenant = $tenant;

        return $this;
    }
}
