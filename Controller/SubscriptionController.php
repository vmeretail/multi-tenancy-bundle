<?php

namespace Tahoe\Bundle\MultiTenancyBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Tahoe\Bundle\MultiTenancyBundle\Form\Type\SubscriptionType;

class SubscriptionController extends Controller
{
    public function indexAction(Request $request)
    {
        $form = $this->createForm(new SubscriptionType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $tenant = $this->get('tahoe.multi_tenancy.tenant_resolver')->getTenant();
            $this->get('tahoe.multi_tenancy.gateway.recurly')->createSubscription($tenant, $form->getData());

            return $this->redirect($this->generateUrl('dashboard_index'));
        }

        return $this->render('TahoeMultiTenancyBundle:Subscription:index.html.twig', array(
            'form' => $form->createView()
        ));
    }
} 