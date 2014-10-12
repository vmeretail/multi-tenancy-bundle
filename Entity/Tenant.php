<?php


namespace Tahoe\Bundle\MultiTenancyBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Tahoe\Bundle\MultiTenancyBundle\Model\MultiTenantTenantInterface;

class Tenant implements MultiTenantTenantInterface
{
    /**
     * @var integer
     */
    protected $id;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $subdomain;

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $subdomain
     *
     * @return $this
     */
    public function setSubdomain($subdomain)
    {
        $this->subdomain = $subdomain;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubdomain()
    {
        return $this->subdomain;
    }

    public function __toString()
    {
        return (string) sprintf('%s (%s)', $this->getName(), $this->getSubdomain());
    }
}
