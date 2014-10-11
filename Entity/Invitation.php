<?php

namespace Tahoe\Bundle\MultiTenancyBundle\Entity;

use Tahoe\Bundle\MultiTenancyBundle\Model\InvitationInterface;
use Tahoe\Bundle\MultiTenancyBundle\Model\OrganizationAwareInterface;
use Tahoe\Bundle\MultiTenancyBundle\Model\OrganizationTrait;

class Invitation implements OrganizationAwareInterface, InvitationInterface
{

    use OrganizationTrait;

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
