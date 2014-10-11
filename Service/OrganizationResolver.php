<?php


namespace Tahoe\Bundle\MultiTenancyBundle\Service;


use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Tahoe\Bundle\MultiTenancyBundle\Model\MultiTenantOrganizationInterface;

class OrganizationResolver
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
    protected $organizationRepository;

    /**
     * @var MultiTenantOrganizationInterface
     */
    protected $organization;

    public function __construct($requestStack, $domain, $organizationRepository)
    {
        $this->requestStack = $requestStack;
        $this->domain = $domain;
        $this->organizationRepository = $organizationRepository;
    }


    /**
     * Returns an organization id based on current url
     *
     * @return int
     * @throws \Exception
     */
    public function getOrganizationId()
    {
        if ($this->organization === null) {
            $this->organization =  $this->resolveOrganization();
        }

        return  $this->organization->getId();
    }


    /**
     * Returns an organization entity based on current url
     *
     * @return MultiTenantOrganizationInterface
     * @throws \Exception
     */
    public function getOrganization()
    {
        if ($this->organization === null) {
            $this->organization =  $this->resolveOrganization();
        }

        return  $this->organization;
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
     * @return MultiTenantOrganizationInterface
     * @throws \Exception
     */
    protected function resolveOrganization()
    {
        $host = $this->requestStack->getCurrentRequest()->getHost();

        if ($host === $this->domain) {
            throw new \Exception('Organization resolver cannot be used in root domain. it only works for sub domains');
        }

        $parts = explode('.', str_replace('.' . $this->domain, '', $host));

        $subdomain = null;

        if (count($parts) === 1 && $parts[0] !== 'www') {
            $subdomain = $parts[0];
        }

        $organization = $this->organizationRepository->findOneBy(array('subdomain' => $subdomain));

        if ($organization) {
            $this->organization = $organization;

            return $this->organization;
        }

        throw new \Exception(sprintf('Organization with sub domain %s doesn\'t exist', $subdomain));
    }
}
