<?php


namespace Tahoe\Bundle\MultiTenancyBundle\Model;
use Tahoe\Bundle\MultiTenancyBundle\Entity\Tenant;

/**
 * Class MultiTenantUserInterface
 *
 * @author Konrad Podgórski <konrad.podgorski@gmail.com>
 */
interface MultiTenantUserInterface
{
    /**
     * @return string
     */
    public function getUsername();

    /**
     * @return Tenant
     */
    public function getActiveTenant();

    /**
     * @param $tenant
     * @return mixed
     */
    public function setActiveTenant($tenant);
}
