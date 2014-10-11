<?php


namespace Tahoe\Bundle\MultiTenancyBundle\Model;

/**
 * Class MultiTenantOrganizationInterface
 *
 * @author Konrad Podgórski <konrad.podgorski@gmail.com>
 */
interface MultiTenantOrganizationInterface
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
