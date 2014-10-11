<?php


namespace Tahoe\Bundle\MultiTenancyBundle\Query\Filter\SQLFilter;

use Doctrine\ORM\Mapping\ClassMetaData,
    Doctrine\ORM\Query\Filter\SQLFilter;

class OrganizationAwareFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if (!$targetEntity->reflClass->implementsInterface('Tahoe\Bundle\MultiTenancyBundle\Model\OrganizationAwareInterface')) {
            return "";
        }

        // it would be easier (and probably less hackish) if I just wrote $column = "organization_id"
        //$column = $targetEntity
        //    ->getAssociationsByTargetClass('Tahoe\Bundle\MultiTenancyBundle\Model\MultiTenantOrganizationInterface')
        //['organization']['targetToSourceKeyColumns']['id'];
        $column = 'organization_id';

        return $targetTableAlias . '.' . $column . ' = ' . $this->getParameter('organizationId');
    }
}
