<?php

namespace Tahoe\Bundle\MultiTenancyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Tahoe\Bundle\MultiTenancyBundle\Model\MultiTenantUserInterface;

class StartController extends Controller
{
    public function indexAction()
    {
        if (!$this->get('tahoe.multi_tenancy.tenant_resolver')->needStartScreen()) {
            return $this->redirect($this->generateUrl('dashboard_index'));
        }
        // if we need start screen, we display it
        $tenantUsers = $this->container->get('tahoe.multi_tenancy.tenant_user_repository')->findBy(
            array(
                'user' => $this->getUser()
            )
        );

        return $this->render(
            'TahoeMultiTenancyBundle:Start:index.html.twig',
            array(
                'tenantUsers' => $tenantUsers
            )
        );
    }
}
