<?php


namespace Tahoe\Bundle\MultiTenancyBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class OrganizationController extends Controller
{
    public function switchDropdownAction()
    {
        $organizationRepository = $this->container->get('tahoe.multi_tenancy.organization_user_repository');

        $organization = $this->container->get('tahoe.multi_tenancy.organization_resolver')->getOrganization();

        $organizationUsers = $organizationRepository->findBy(
            array(
                'user' => $this->getUser()
            )
        );

        return $this->render(
            'TahoeMultiTenancyBundle:Organization:_switch_dropdown.html.twig',
            array(
                'organization' => $organization,
                'organizationUsers' => $organizationUsers
            )
        );
    }

    public function switchOrganizationAction($subdomain)
    {
        $filters = $this->getDoctrine()->getManager()->getFilters();

        // for a moment we need to disable organization aware filter to fetch invitations from all tenants
        $filters->disable("organizationAware");
        $organization = $this->container->get('organization_repository')->findOneBy(array(
                'subdomain' => $subdomain
            ));
        $filters->enable("organizationAware");

        $url = $this->container
            ->get('tahoe.multi_tenancy.organization_aware_router')->generateUrl($organization, 'dashboard_index');

        return $this->redirect($url);
    }
}
