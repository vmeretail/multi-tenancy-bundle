<?php

namespace Tahoe\Bundle\MultiTenancyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StartController extends Controller
{
    public function indexAction()
    {
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
