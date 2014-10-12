<?php


namespace Tahoe\Bundle\MultiTenancyBundle\Query\Filter\SQLFilter;

use Doctrine\ORM\Mapping\ClassMetaData,
    Doctrine\ORM\Query\Filter\SQLFilter;

class TenantAwareFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if (!$targetEntity->reflClass->implementsInterface('Tahoe\Bundle\MultiTenancyBundle\Model\TenantAwareInterface')) {
            return "";
        }

        // it would be easier (and probably less hackish) if I just wrote $column = "tenant_id"
        //$column = $targetEntity
        //    ->getAssociationsByTargetClass('Tahoe\Bundle\MultiTenancyBundle\Model\MultiTenantTenantInterface')
        //['tenant']['targetToSourceKeyColumns']['id'];
        $column = 'tenant_id';

        return $targetTableAlias . '.' . $column . ' = ' . $this->getParameter('tenantId');
    }
}
