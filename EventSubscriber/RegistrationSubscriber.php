<?php

namespace Tahoe\Bundle\MultiTenancyBundle\EventSubscriber;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tahoe\Bundle\MultiTenancyBundle\Gateway\GatewayManagerInterface;
use Tahoe\Bundle\MultiTenancyBundle\Handler\TenantUserHandler;
use Tahoe\Bundle\MultiTenancyBundle\Service\TenantAwareRouter;
use Tahoe\XfrifyBundle\Factory\FactoryInterface;

/**
 * Class RegistrationSubscriber
 *
 * Responsible for creating tenant during registration, it also add just created user as an tenant admin
 *
 * @author Konrad Podgórski <konrad.podgorski@gmail.com>
 */
class RegistrationSubscriber implements EventSubscriberInterface
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var FactoryInterface
     */
    protected $tenantFactory;

    /**
     * @var TenantUserHandler
     */
    protected $tenantUserHandler;

    /**
     * @var TenantAwareRouter
     */
    protected $tenantAwareRouter;

    /**
     * @var FormInterface
     */
    private $_form;

    /**
     * @var RedirectResponse
     */
    protected $redirectResponse;

    /** @var  GatewayManagerInterface */
    protected $gatewayManager;

    function __construct($entityManager, $tenantFactory, $tenantUserHandler, $tenantAwareRouter, $gatewayManager)
    {
        $this->entityManager = $entityManager;
        $this->tenantFactory = $tenantFactory;
        $this->tenantUserHandler = $tenantUserHandler;
        $this->tenantAwareRouter = $tenantAwareRouter;
        $this->gatewayManager = $gatewayManager;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
            FOSUserEvents::REGISTRATION_COMPLETED => 'onRegistrationCompleted',
        );
    }

    /**
     * Used only to get form reference because it's not available in the next event, onRegistrationCompleted
     * @param FormEvent $event
     */
    public function onRegistrationSuccess(FormEvent $event)
    {
        /**
         * Disclaimer: Subscriber does all it's magic in onRegistrationCompleted method,
         * however in onRegistrationCompleted we don't have access form (so we can get tenant name and subdomain)
         * and http response (so we can redirect user to his new tenant instance)
         *
         * That's why we are using other event that is fired before onRegistrationCompleted and we grab references to
         * form and response objects that will be used in that next event.
         */

        $this->_form = $event->getForm();

        // we get reference to the redirect response that will be used in another event
        $this->redirectResponse = new RedirectResponse('dummy');
        // FOS User Registration controller check if response is set in event, if so it will just use it.
        $event->setResponse($this->redirectResponse);
    }

    public function onRegistrationCompleted(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();

        $tenantName = $this->_form->get('tenantName')->getData();
        $tenantSubdomain = $this->_form->get('tenantSubdomain')->getData();

        $tenant = $this->tenantFactory->createNew();
        $tenant->setName($tenantName);
        $tenant->setSubdomain($tenantSubdomain);

        $this->entityManager->persist($tenant);
        $this->entityManager->flush();

        $this->tenantUserHandler->addUserToTenant($user, $tenant, array('ROLE_ADMIN'));
        $this->entityManager->flush();

        // we create a new account for gateway
        $this->gatewayManager->createAccount($tenant);

        // this referenced redirect response will be used
        $this->redirectResponse
            ->setTargetUrl($this->tenantAwareRouter->generateUrl($tenant, 'dashboard_index'));

        unset($this->_form);
    }
}