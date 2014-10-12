<?php


namespace Tahoe\Bundle\MultiTenancyBundle\EventSubscriber;


use Doctrine\Common\EventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\Container;

use Tahoe\Bundle\MultiTenancyBundle\Model\TenantAwareInterface;
use Tahoe\Bundle\MultiTenancyBundle\Service\TenantResolver;

class TenantAwareSubscriber implements EventSubscriber
{
    /**
     * @var TenantResolver
     */
    protected $tenantResolver;
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
        /** @var TenantAwareInterface $object */
        $object = $args->getObject();

        // lazy loading to solve circular reference exception
        if ($this->tenantResolver == null) {
            $this->tenantResolver = $this->container->get('tahoe.multi_tenancy.tenant_resolver');
        }

        if ($object instanceof TenantAwareInterface) {
            if ($object->getTenant() === null) {
                $object->setTenant($this->tenantResolver->getTenant());
            }
        }
    }
}