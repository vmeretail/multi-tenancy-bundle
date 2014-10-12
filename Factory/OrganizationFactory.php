<?php

namespace Tahoe\Bundle\MultiTenancyBundle\Factory;


class OrganizationFactory
{
    private $class;

    public function __construct($class)
    {
        $this->class = $class;
    }


    public function createNew()
    {
        return new $this->class;
    }
} 