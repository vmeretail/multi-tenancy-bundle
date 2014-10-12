<?php


namespace Tahoe\Bundle\MultiTenancyBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Tahoe\Bundle\MultiTenancyBundle\Service\TenantResolver;

class TenantAwareListener implements EventSubscriberInterface {
    /**
     * @var EntityManager
     */
    protected $entityManager;
    /**
     * @var TenantResolver
     */
    protected $tenantResolver;

    public function __construct($entityManager, $tenantResolver)
    {
        $this->entityManager = $entityManager;
        $this->tenantResolver = $tenantResolver;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        if (false === $this->tenantResolver->isSubdomain()) {
            return;
        }

        $tenantId = $this->tenantResolver->getTenantId();

        $this->entityManager->getFilters()->enable('tenantAware')
            ->setParameter('tenantId', $tenantId);
    }

    public static function getSubscribedEvents()
    {
        return array(KernelEvents::CONTROLLER => array('onKernelController'));
    }
}
