<?php

namespace Tahoe\Bundle\MultiTenancyBundle\Entity;

use Tahoe\Bundle\MultiTenancyBundle\Model\InvitationInterface;
use Tahoe\Bundle\MultiTenancyBundle\Model\TenantAwareInterface;
use Tahoe\Bundle\MultiTenancyBundle\Model\TenantTrait;

class Invitation implements TenantAwareInterface, InvitationInterface
{

    use TenantTrait;

    /**
     * @var integer
     */
    protected $id;
    /**
     * @var string
     */
    protected $email;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getEmail();
    }
}
