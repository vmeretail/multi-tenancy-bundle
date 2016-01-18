<?php


namespace Tahoe\Bundle\MultiTenancyBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Router;
use Tahoe\Bundle\MultiTenancyBundle\Model\MultiTenantTenantInterface;

/**
 * Class TenantAwareRouter that cares about tenants! :-)
 *
 * @author Konrad Podgórski <konrad.podgorski@gmail.com>
 */
class TenantAwareRouter
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

    protected $strategy;

    protected $redirectRoute;

    function __construct($requestStack, $domain , $router, $strategy, $route = '')
    {
        $this->requestStack = $requestStack;
        $this->domain = $domain;
        $this->router = $router;
        $this->strategy = $strategy;
        $this->redirectRoute = $route;
    }

    /**
     * @param MultiTenantTenantInterface $tenant
     * @param string                           $name
     * @param array                            $parameters
     *
     * @return string
     */
    public function generateUrl(MultiTenantTenantInterface $tenant, $parameters = array())
    {
        $url = '';
        $name = $this->redirectRoute;

        if ($this->strategy == TenantResolver::STRATEGY_TENANT_AWARE_SUBDOMAIN ) {
            $scheme = $this->requestStack->getCurrentRequest()->getScheme();
            $requestPort = $this->requestStack->getCurrentRequest()->getPort();

            $host = $scheme . '://' . $tenant->getSubdomain() . '.' . $this->domain;

            $port = '';
            if ('http' === $scheme && 80 != $requestPort) {
                $port = ':'.$requestPort;
            } elseif ('https' === $scheme && 443 != $requestPort) {
                $port = ':'.$requestPort;
            }

            $url = $host . $port;
            $url .= $this->router->generate($name, $parameters, self::ABSOLUTE_URL_FALSE);

            return $url;
        } else {
            return $this->router->generate($name, $parameters);
        }
    }
}
