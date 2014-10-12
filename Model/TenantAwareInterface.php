<?php


namespace Tahoe\Bundle\MultiTenancyBundle\Model;

/**
 * Class TenantAwareInterface
 *
 * @author Konrad Podgórski <konrad.podgorski@gmail.com>
 */
interface TenantAwareInterface
{
    /**
     * @return MultiTenantTenantInterface
     */
    public function getTenant();

    /**
     * @param MultiTenantTenantInterface $tenant
     *
     * @return $this
     */
    public function setTenant($tenant);
}
