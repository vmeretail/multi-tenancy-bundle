<?php


namespace Tahoe\Bundle\MultiTenancyBundle\EventSubscriber;


use Doctrine\Common\EventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\Container;

use Tahoe\Bundle\MultiTenancyBundle\Model\OrganizationAwareInterface;
use Tahoe\Bundle\MultiTenancyBundle\Service\OrganizationResolver;

class OrganizationAwareSubscriber implements EventSubscriber
{
    /**
     * @var OrganizationResolver
     */
    protected $organizationResolver;
    /**
     * @var Container
     */
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getSubscribedEvents()
    {
        return array(
            Events::prePersist
        );
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        /** @var OrganizationAwareInterface $object */
        $object = $args->getObject();

        // lazy loading to solve circular reference exception
        if ($this->organizationResolver == null) {
            $this->organizationResolver = $this->container->get('tahoe.multi_tenancy.organization_resolver');
        }

        if ($object instanceof OrganizationAwareInterface) {
            if ($object->getOrganization() === null) {
                $object->setOrganization($this->organizationResolver->getOrganization());
            }
        }
    }
}