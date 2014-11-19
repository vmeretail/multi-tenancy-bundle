<?php


namespace Tahoe\Bundle\MultiTenancyBundle\Service;


use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Tahoe\Bundle\MultiTenancyBundle\Entity\Tenant;
use Tahoe\Bundle\MultiTenancyBundle\Model\MultiTenantTenantInterface;
use Tahoe\Bundle\MultiTenancyBundle\Model\MultiTenantUserInterface;

class TenantResolver
{
    const STRATEGY_TENANT_AWARE_SUBDOMAIN = 'tenant_aware';
    const STRATEGY_FIXED_SUBDOMAIN = 'fixed';

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

    protected $strategy;

    protected $token;

    public function __construct($requestStack, $domain, $tenantRepository, TokenStorage $token, $strategy)
    {
        $this->requestStack = $requestStack;
        $this->domain = $domain;
        $this->tenantRepository = $tenantRepository;
        $this->token = $token;
        $this->strategy = $strategy;
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

        return ($this->tenant) ? $this->tenant->getId() : null;
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
        // we check which strategy was chosen to resolve the tenant
        if ($this->strategy == self::STRATEGY_TENANT_AWARE_SUBDOMAIN ) {
            return $this->resolveTenantFromSubdomain();
        } else if ($this->strategy == self::STRATEGY_FIXED_SUBDOMAIN) {
            return $this->resolveTenantFromUser();
        }

        // we don't know the strategy, we thrown an exception here
        throw new \Exception('Strategy unknown! Please provide the correct strategy.');
    }

    public function getStrategy()
    {
        return $this->strategy;
    }

    public function needStartScreen()
    {
        /** @var MultiTenantUserInterface $user */
        $user = $this->token->getToken()->getUser();
        return ($this->strategy == self::STRATEGY_FIXED_SUBDOMAIN and !$user->getActiveTenant())
            or ($this->strategy == self::STRATEGY_TENANT_AWARE_SUBDOMAIN);
    }

    /**
     * @return Tenant
     */
    protected function resolveTenantFromUser()
    {
        $token = $this->token->getToken();
        return ($token && is_object($token->getUser())) ? $token->getUser()->getActiveTenant() : null;
    }

    /**
     * @return Tenant
     * @throws \Exception
     */
    protected function resolveTenantFromSubdomain()
    {
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
