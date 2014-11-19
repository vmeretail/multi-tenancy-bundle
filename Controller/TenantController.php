<?php


namespace Tahoe\Bundle\MultiTenancyBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Tahoe\Bundle\MultiTenancyBundle\Model\MultiTenantTenantInterface;
use Tahoe\Bundle\MultiTenancyBundle\Model\MultiTenantUserInterface;
use Tahoe\Bundle\MultiTenancyBundle\Service\TenantResolver;

class TenantController extends Controller
{
    public function switchDropdownAction()
    {
        $tenantRepository = $this->container->get('tahoe.multi_tenancy.tenant_user_repository');

        $tenant = $this->container->get('tahoe.multi_tenancy.tenant_resolver')->getTenant();

        $tenantUsers = $tenantRepository->findBy(
            array(
                'user' => $this->getUser()
            )
        );

        return $this->render(
            'TahoeMultiTenancyBundle:Tenant:_switch_dropdown.html.twig',
            array(
                'tenant' => $tenant,
                'tenantUsers' => $tenantUsers
            )
        );
    }

    public function switchTenantAction($subdomain)
    {
        /** @var MultiTenantUserInterface $user */
        $user = $this->getUser();
        $filters = $this->getDoctrine()->getManager()->getFilters();

        // for a moment we need to disable tenant aware filter to fetch invitations from all tenants
        $filters->disable("tenantAware");
        $tenant = $this->container->get('tenant_repository')->findOneBy(array(
                'subdomain' => $subdomain
            ));
        $filters->enable("tenantAware");

        if ($this->get('tahoe.multi_tenancy.tenant_resolver')->getStrategy() == TenantResolver::STRATEGY_FIXED_SUBDOMAIN) {
            // we update the tenant in the user and we redirect to the dashboard
            $user->setActiveTenant($tenant);
            $this->get('doctrine.orm.entity_manager')->flush();

            return $this->redirect($this->generateUrl('dashboard_index'));
        }

        $url = $this->container
            ->get('tahoe.multi_tenancy.tenant_aware_router')->generateUrl($tenant, 'dashboard_index');

        return $this->redirect($url);
    }
}
