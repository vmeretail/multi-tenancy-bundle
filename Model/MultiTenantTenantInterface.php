<?php


namespace Tahoe\Bundle\MultiTenancyBundle\Model;

/**
 * Class MultiTenantTenantInterface
 *
 * @author Konrad Podgórski <konrad.podgorski@gmail.com>
 */
interface MultiTenantTenantInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getSubdomain();
}
