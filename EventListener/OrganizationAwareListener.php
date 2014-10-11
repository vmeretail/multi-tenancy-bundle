<?php


namespace Tahoe\Bundle\MultiTenancyBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Tahoe\Bundle\MultiTenancyBundle\Service\OrganizationResolver;

class OrganizationAwareListener implements EventSubscriberInterface {
    /**
     * @var EntityManager
     */
    protected $entityManager;
    /**
     * @var OrganizationResolver
     */
    protected $organizationResolver;

    public function __construct($entityManager, $organizationResolver)
    {
        $this->entityManager = $entityManager;
        $this->organizationResolver = $organizationResolver;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        if (false === $this->organizationResolver->isSubdomain()) {
            return;
        }

        $organizationId = $this->organizationResolver->getOrganizationId();

        $this->entityManager->getFilters()->enable('organizationAware')
            ->setParameter('organizationId', $organizationId);
    }

    public static function getSubscribedEvents()
    {
        return array(KernelEvents::CONTROLLER => array('onKernelController'));
    }
}
