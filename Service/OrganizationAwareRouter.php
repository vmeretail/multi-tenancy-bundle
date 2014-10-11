<?php


namespace Tahoe\Bundle\MultiTenancyBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Router;
use Tahoe\Bundle\MultiTenancyBundle\Model\MultiTenantOrganizationInterface;

/**
 * Class OrganizationAwareRouter that cares about organizations! :-)
 *
 * @author Konrad Podgórski <konrad.podgorski@gmail.com>
 */
class OrganizationAwareRouter
{
    const ABSOLUTE_URL_FALSE = false;
    /**
     * @var RequestStack
     */
    protected $requestStack;
    /**
     * @var string
     */
    protected $domain;
    /**
     * @var Router
     */
    protected $router;

    function __construct($requestStack, $domain , $router)
    {
        $this->requestStack = $requestStack;
        $this->domain = $domain;
        $this->router = $router;
    }

    /**
     * @param MultiTenantOrganizationInterface $organization
     * @param string                           $name
     * @param array                            $parameters
     *
     * @return string
     */
    public function generateUrl(MultiTenantOrganizationInterface $organization, $name, $parameters = array())
    {
        $scheme = $this->requestStack->getCurrentRequest()->getScheme();
        $requestPort = $this->requestStack->getCurrentRequest()->getPort();

        $host = $scheme . '://' . $organization->getSubdomain() . '.' . $this->domain;

        $port = '';
        if ('http' === $scheme && 80 != $requestPort) {
            $port = ':'.$requestPort;
        } elseif ('https' === $scheme && 443 != $requestPort) {
            $port = ':'.$requestPort;
        }

        $url = $host . $port;

        $url .= $this->router->generate($name, $parameters, self::ABSOLUTE_URL_FALSE);

        return $url;
    }
}
