<?php


namespace Tahoe\Bundle\MultiTenancyBundle\Service;


use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Tahoe\Bundle\MultiTenancyBundle\Entity\Tenant;
use Tahoe\Bundle\MultiTenancyBundle\Model\MultiTenantTenantInterface;

class TenantResolver
{
    /**
     * @var RequestStack
     */
    protected $requestStack;
    /**
     * @var string
     */
    protected $domain;

    /**
     * @var EntityRepository
     */
    protected $tenantRepository;

    /**
     * @var MultiTenantTenantInterface
     */
    protected $tenant;

    public function __construct($requestStack, $domain, $tenantRepository)
    {
        $this->requestStack = $requestStack;
        $this->domain = $domain;
        $this->tenantRepository = $tenantRepository;
    }


    /**
     * Returns an tenant id based on current url
     *
     * @return int
     * @throws \Exception
     */
    public function getTenantId()
    {
        if ($this->tenant === null) {
            $this->tenant =  $this->resolveTenant();
        }

        return  $this->tenant->getId();
    }


    /**
     * Returns an tenant entity based on current url
     *
     * @return MultiTenantTenantInterface
     * @throws \Exception
     */
    public function getTenant()
    {
        if ($this->tenant === null) {
            $this->tenant =  $this->resolveTenant();
        }

        return  $this->tenant;
    }

    public function isSubdomain()
    {
        $host = $this->requestStack->getCurrentRequest()->getHost();

        $parts = explode('.', str_replace('.' . $this->domain, '', $host));

        $subdomain = null;

        if (count($parts) === 1 && $parts[0] !== 'www') {
            $subdomain = $parts[0];
        }

        return !empty($subdomain);
    }

    /**
     * @return MultiTenantTenantInterface
     * @throws \Exception
     */
    protected function resolveTenant()
    {
        // we check if tenant was setted by the override method
        if ($this->tenant) {
            return $this->tenant;
        }
        // if not, we resolve it by the subdomain
        $host = $this->requestStack->getCurrentRequest()->getHost();

        if ($host === $this->domain) {
            throw new \Exception('Tenant resolver cannot be used in root domain. it only works for sub domains');
        }

        $parts = explode('.', str_replace('.' . $this->domain, '', $host));

        $subdomain = null;

        if (count($parts) === 1 && $parts[0] !== 'www') {
            $subdomain = $parts[0];
        }

        $tenant = $this->tenantRepository->findOneBy(array('subdomain' => $subdomain));

        if ($tenant) {
            $this->tenant = $tenant;

            return $this->tenant;
        }

        throw new \Exception(sprintf('Tenant with sub domain %s doesn\'t exist', $subdomain));
    }

    /** 
     * @param Tenant $tenant 
     * 
     * @return $this 
     */
    public function overrideTenant(Tenant $tenant)
    {
        $this->tenant = $tenant;
        return $this;
    }
}
