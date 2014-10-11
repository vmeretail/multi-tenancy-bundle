<?php

namespace Tahoe\Bundle\MultiTenancyBundle\Entity;

/**
 * OrganizationUser
 */
class OrganizationUser
{
    const ROLE_ORGANIZATION_MANAGER = 'ROLE_ORGANIZATION_MANAGER';
    const ROLE_ORGANIZATION_CUSTOMER = 'ROLE_ORGANIZATION_CUSTOMER';

    /**
     * @var Organization $organization
     */
    protected $organization;

    /**
     * @var User $user
     */
    protected $user;

    /**
     * @var array $roles
     */
    protected $roles;

    public function __construct()
    {
        $this->roles = array(); // must be an array NOT ArrayCollection
    }

    /**
     * @param array $roles
     *
     * @return $this
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Available role constants:
     * - OrganizationUser::ROLE_ORGANIZATION_MANAGER
     * - OrganizationUser::ROLE_ORGANIZATION_CUSTOMER
     *
     * @param string $role
     * @return $this
     */
    public function addRole($role)
    {
        $role = strtoupper($role);

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @param string $role
     *
     * @return $this
     */
    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * @param string $role
     *
     * @return boolean
     */
    public function hasRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            return true;
        }

        return false;
    }

    /**
     * @param \Tahoe\Bundle\MultiTenancyBundle\Model\MultiTenantUserInterface $user
     *
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return \Tahoe\Bundle\MultiTenancyBundle\Model\MultiTenantUserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \Tahoe\Bundle\MultiTenancyBundle\Entity\Organization $organization
     *
     * @return $this
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * @return \Tahoe\Bundle\MultiTenancyBundle\Entity\Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }
}
