<?php


namespace Tahoe\Bundle\MultiTenancyBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
        $filters = $this->getDoctrine()->getManager()->getFilters();

        // for a moment we need to disable tenant aware filter to fetch invitations from all tenants
        $filters->disable("tenantAware");
        $tenant = $this->container->get('tenant_repository')->findOneBy(array(
                'subdomain' => $subdomain
            ));
        $filters->enable("tenantAware");

        $url = $this->container
            ->get('tahoe.multi_tenancy.tenant_aware_router')->generateUrl($tenant, 'dashboard_index');

        return $this->redirect($url);
    }
}
