<?php

namespace Tahoe\Bundle\MultiTenancyBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Tahoe\Bundle\MultiTenancyBundle\Form\Type\SubscriptionType;

class SubscriptionController extends Controller
{
    public function indexAction(Request $request)
    {
        $tenant = $this->get('tahoe.multi_tenancy.tenant_resolver')->getTenant();
        if ($this->get('tahoe.multi_tenancy.gateway.recurly')->subscriptionExists($tenant)) {
            return $this->redirect($this->generateUrl('tahoe_multitenancy_subscription_details'));
        }
        $form = $this->createForm(new SubscriptionType(), new \Recurly_BillingInfo());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('tahoe.multi_tenancy.gateway.recurly')->createSubscription($tenant, $form->getData());

            $this->get('session')->getFlashBag()->add('success', 'The subscription has been created successfully');

            return $this->redirect($this->generateUrl('tahoe_multitenancy_subscription'));
        }

        return $this->render('TahoeMultiTenancyBundle:Subscription:index.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function detailsAction(Request $request)
    {
        $tenant = $this->get('tahoe.multi_tenancy.tenant_resolver')->getTenant();
        $subscription = $this->get('tahoe.multi_tenancy.gateway.recurly')->getSubscription($tenant);
        if (null === $subscription) {
            return $this->redirect($this->generateUrl('tahoe_multitenancy_subscription'));
        }

        $billingInfo = $this->get('tahoe.multi_tenancy.gateway.recurly')->getBillingInfo($tenant);

        $form = $this->createForm(new SubscriptionType(), $billingInfo);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('tahoe.multi_tenancy.gateway.recurly')->updateBillingInfo($tenant, $form->getData());
            $this->get('session')->getFlashBag()->add('success', 'Your billing information has been updated successfully');

            return $this->redirect($this->generateUrl('tahoe_multitenancy_subscription'));
        }

        return $this->render('TahoeMultiTenancyBundle:Subscription:index.html.twig', array(
            'form' => $form->createView(),
            'update' => true
        ));
    }
} 