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
use Tahoe\Bundle\MultiTenancyBundle\Handler\OrganizationUserHandler;
use Tahoe\Bundle\MultiTenancyBundle\Service\OrganizationAwareRouter;
use Tahoe\XfrifyBundle\Factory\FactoryInterface;

/**
 * Class RegistrationSubscriber
 *
 * Responsible for creating organization during registration, it also add just created user as an organization admin
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
    protected $organizationFactory;

    /**
     * @var OrganizationUserHandler
     */
    protected $organizationUserHandler;

    /**
     * @var OrganizationAwareRouter
     */
    protected $organizationAwareRouter;

    /**
     * @var FormInterface
     */
    private $_form;

    /**
     * @var RedirectResponse
     */
    protected $redirectResponse;

    function __construct($entityManager, $organizationFactory, $organizationUserHandler, $organizationAwareRouter)
    {
        $this->entityManager = $entityManager;
        $this->organizationFactory = $organizationFactory;
        $this->organizationUserHandler = $organizationUserHandler;
        $this->organizationAwareRouter = $organizationAwareRouter;
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
         * however in onRegistrationCompleted we don't have access form (so we can get organization name and subdomain)
         * and http response (so we can redirect user to his new organization instance)
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

        $organizationName = $this->_form->get('organizationName')->getData();
        $organizationSubdomain = $this->_form->get('organizationSubdomain')->getData();

        $organization = $this->organizationFactory->createNew();
        $organization->setName($organizationName);
        $organization->setSubdomain($organizationSubdomain);

        $this->entityManager->persist($organization);
        $this->entityManager->flush();

        $this->organizationUserHandler->addUserToOrganization($user, $organization, array('ROLE_ADMIN'));
        $this->entityManager->flush();

        // this referenced redirect response will be used
        $this->redirectResponse
            ->setTargetUrl($this->organizationAwareRouter->generateUrl($organization, 'dashboard_index'));

        unset($this->_form);
    }
}