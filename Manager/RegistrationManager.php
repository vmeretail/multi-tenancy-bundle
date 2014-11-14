<?php

namespace Tahoe\Bundle\MultiTenancyBundle\Manager;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormInterface;
use Tahoe\Bundle\MultiTenancyBundle\Entity\Tenant;
use Tahoe\Bundle\MultiTenancyBundle\Factory\TenantFactory;
use Tahoe\Bundle\MultiTenancyBundle\Gateway\GatewayManagerInterface;
use Tahoe\Bundle\MultiTenancyBundle\Handler\TenantUserHandler;
use Tahoe\Bundle\MultiTenancyBundle\Model\MultiTenantUserInterface;
use Tahoe\Bundle\MultiTenancyBundle\Service\TenantAwareRouter;

class RegistrationManager
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var TenantFactory
     */
    protected $tenantFactory;

    /**
     * @var TenantUserHandler
     */
    protected $tenantUserHandler;

    /** @var  GatewayManagerInterface */
    protected $gatewayManager;

    function __construct($entityManager, $tenantFactory, $tenantUserHandler, $gatewayManager)
    {
        $this->entityManager = $entityManager;
        $this->tenantFactory = $tenantFactory;
        $this->tenantUserHandler = $tenantUserHandler;
        $this->gatewayManager = $gatewayManager;
    }

    /**
     * @param MultiTenantUserInterface $user
     * @param FormInterface $form
     *
     * @return Tenant
     * @throws \Exception
     */
    public function createTenant(MultiTenantUserInterface $user, $tenantName, $tenantSubdomain)
    {
        /** @var Tenant $tenant */
        $tenant = $this->tenantFactory->createNew();
        $tenant->setName($tenantName);
        $tenant->setSubdomain($tenantSubdomain);

        $this->entityManager->persist($tenant);
        $this->entityManager->flush();

        $this->tenantUserHandler->addUserToTenant($user, $tenant, array('ROLE_ADMIN'));
        $this->entityManager->flush();

        // we create a new account for gateway
        $this->gatewayManager->createAccount($tenant);

        return $tenant;
    }
}
