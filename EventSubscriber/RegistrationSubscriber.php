<?php

namespace Tahoe\Bundle\MultiTenancyBundle\EventSubscriber;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tahoe\Bundle\MultiTenancyBundle\Manager\RegistrationManager;
use Tahoe\Bundle\MultiTenancyBundle\Service\TenantAwareRouter;

/**
 * Class RegistrationSubscriber
 *
 * Responsible for creating tenant during registration, it also add just created user as an tenant admin
 *
 * @author Konrad Podgórski <konrad.podgorski@gmail.com>
 */
class RegistrationSubscriber implements EventSubscriberInterface, RegistrationSubscriberInterface
{
    /**
     * @var FormInterface
     */
    private $_form;

    /**
     * @var RedirectResponse
     */
    protected $redirectResponse;

    protected $registrationManager;

    protected $tenantAwareRouter;

    function __construct(RegistrationManager $registrationManager)
    {
        $this->registrationManager = $registrationManager;

    }

    public function setRouter($router)
    {
        $this->tenantAwareRouter = $router;
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

        $tenant = $this->registrationManager->createTenant($user, $tenantName, $tenantSubdomain);

        // this referenced redirect response will be used
        $this->redirectResponse
            ->setTargetUrl($this->tenantAwareRouter->generateUrl($tenant, 'dashboard_index'));

        unset($this->_form);
    }
}