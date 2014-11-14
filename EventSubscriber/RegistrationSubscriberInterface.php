<?php

namespace Tahoe\Bundle\MultiTenancyBundle\EventSubscriber;


use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;

interface RegistrationSubscriberInterface
{
    public function setRouter($router);

    public function onRegistrationSuccess(FormEvent $event);

    public function onRegistrationCompleted(FilterUserResponseEvent $event);
} 