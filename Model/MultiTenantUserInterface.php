<?php


namespace Tahoe\Bundle\MultiTenancyBundle\Model;

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
}
