<?php

namespace Tahoe\Bundle\MultiTenancyBundle\Manager;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
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
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param EntityManager $entityManager
     * @param TenantFactory $tenantFactory
     * @param TenantUserHandler $tenantUserHandler
     * @param LoggerInterface $logger
     * @param GatewayManagerInterface $gatewayManager
     */
    public function __construct(EntityManager $entityManager, TenantFactory $tenantFactory,
                                TenantUserHandler $tenantUserHandler, LoggerInterface $logger,
                                GatewayManagerInterface $gatewayManager = null)
    {
        $this->entityManager = $entityManager;
        $this->tenantFactory = $tenantFactory;
        $this->tenantUserHandler = $tenantUserHandler;
        $this->gatewayManager = $gatewayManager;
        $this->logger = $logger;
    }

    /**
     * @param MultiTenantUserInterface $user
     * @param FormInterface $form
     *
     * @return Tenant
     * @throws \Exception
     */
    public function createTenant(MultiTenantUserInterface $user, $tenantName, $tenantSubdomain = '')
    {
        $this->logger->info(sprintf('Creating new tenant with name %s and subdomain %s',
                $tenantName, $tenantSubdomain));

        /** @var Tenant $tenant */
        $tenant = $this->tenantFactory->createNew();
        $tenant->setName($tenantName);
        $tenantSubdomain = $tenantSubdomain ?: $this->createSubdomainFromTenant($tenantName);
        $tenant->setSubdomain($tenantSubdomain);

        $this->entityManager->persist($tenant);
        // add as active tenant
        $user->setActiveTenant($tenant);
        $this->entityManager->flush();

        $this->tenantUserHandler->addUserToTenant($user, $tenant, array('ROLE_ADMIN'));
        $this->entityManager->flush();

        // we create a new account for gateway
        if (null !== $this->gatewayManager) {
            $this->logger->info(sprintf('Creating new account for tenant using gatewaymanager %s',
                    get_class($this->gatewayManager)));
            $this->gatewayManager->createAccount($tenant);
        } else {
            $this->logger->info('No gatewaymanager configured');
        }

        return $tenant;
    }

    /**
     * @param $tenantName
     * @return mixed|string
     */
    private function createSubdomainFromTenant($tenantName)
    {
        // replace non letter or digits by -
        $subdomain = preg_replace('~[^\\pL\d]+~u', '-', $tenantName);

        // trim
        $subdomain = trim($subdomain, '-');

        // transliterate
        $subdomain = iconv('utf-8', 'us-ascii//TRANSLIT', $subdomain);

        // lowercase
        $subdomain = strtolower($subdomain);

        // remove unwanted characters
        $subdomain = preg_replace('~[^-\w]+~', '', $subdomain);

        if (empty($subdomain))
        {
            return 'n-a';
        }

        return $subdomain;
    }
}
