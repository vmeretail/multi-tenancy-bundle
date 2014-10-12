<?php

namespace Tahoe\Bundle\MultiTenancyBundle\Entity;

/**
 * TenantUser
 */
class TenantUser
{
    const ROLE_TENANT_MANAGER = 'ROLE_TENANT_MANAGER';
    const ROLE_TENANT_CUSTOMER = 'ROLE_TENANT_CUSTOMER';

    /**
     * @var Tenant $tenant
     */
    protected $tenant;

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
     * - TenantUser::ROLE_TENANT_MANAGER
     * - TenantUser::ROLE_TENANT_CUSTOMER
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
     * @param \Tahoe\Bundle\MultiTenancyBundle\Entity\Tenant $tenant
     *
     * @return $this
     */
    public function setTenant($tenant)
    {
        $this->tenant = $tenant;

        return $this;
    }

    /**
     * @return \Tahoe\Bundle\MultiTenancyBundle\Entity\Tenant
     */
    public function getTenant()
    {
        return $this->tenant;
    }
}
