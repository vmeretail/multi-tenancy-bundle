<?php

namespace Tahoe\Bundle\MultiTenancyBundle\Factory;


class TenantFactory
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