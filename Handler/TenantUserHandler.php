<?php


namespace Tahoe\Bundle\MultiTenancyBundle\Handler;


use Doctrine\ORM\EntityManager;
use Tahoe\Bundle\MultiTenancyBundle\Factory\TenantUserFactory;
use Tahoe\Bundle\MultiTenancyBundle\Model\InvitationInterface;
use Tahoe\Bundle\MultiTenancyBundle\Model\MultiTenantTenantInterface;
use Tahoe\Bundle\MultiTenancyBundle\Model\MultiTenantUserInterface;
use Tahoe\Bundle\MultiTenancyBundle\Repository\TenantUserRepository;

/**
 * Class TenantUserHandler
 *
 * @author Konrad Podgórski <konrad.podgorski@gmail.com>
 */
class TenantUserHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;
    /**
     * @var TenantUserRepository
     */
    protected $tenantUserRepository;
    /**
     * @var TenantUserFactory
     */
    protected $tenantUserFactory;

    function __construct($entityManager, $tenantUserFactory, $tenantUserRepository)
    {
        $this->entityManager = $entityManager;
        $this->tenantUserFactory = $tenantUserFactory;
        $this->tenantUserRepository = $tenantUserRepository;
    }

    /**
     * @param MultiTenantUserInterface         $user
     * @param MultiTenantTenantInterface $tenant
     * @param array                            $roles
     *
     * @return \Tahoe\Bundle\MultiTenancyBundle\Entity\TenantUser
     * @throws \Exception
     */
    public function addUserToTenant(
        MultiTenantUserInterface $user,
        MultiTenantTenantInterface $tenant,
        $roles = array()
    ) {
        $tenantUser = $this->tenantUserRepository->findOneBy(
            array(
                'tenant' => $tenant,
                'user' => $user
            )
        );

        if ($tenantUser) {
            // user is already a member of this tenant

            throw new \Exception(sprintf('User is already a member of given tenant'));
        }

        $tenantUser = $this->tenantUserFactory->createNew();
        $tenantUser->setUser($user);
        $tenantUser->setTenant($tenant);
        $tenantUser->setRoles($roles);

        $this->entityManager->persist($tenantUser);

        return $tenantUser;
    }

    /**
     * @param MultiTenantUserInterface         $user
     * @param MultiTenantTenantInterface $tenant
     *
     * @return bool
     * @throws \Exception
     */
    public function removeUserFromTenant(
        MultiTenantUserInterface $user,
        MultiTenantTenantInterface $tenant
    ) {
        $tenantUser = $this->tenantUserRepository->findOneBy(
            array(
                'tenant' => $tenant,
                'user' => $user
            )
        );

        if ($tenantUser) {
            $this->entityManager->remove($tenantUser);
            $this->entityManager->flush();

            return true;
        }

        throw new \Exception(sprintf('User is not a member of given tenant'));
    }

    public function addRoleToUserInTenant(
        $role,
        MultiTenantUserInterface $user,
        MultiTenantTenantInterface $tenant)
    {
        $tenantUser = $this->tenantUserRepository->findOneBy(
            array(
                'tenant' => $tenant,
                'user' => $user
            )
        );

        if ($tenantUser) {

            if (false === $tenantUser->hasRole($role)) {
                $tenantUser->addRole($role);
            }

            $this->entityManager->persist($tenantUser);
            $this->entityManager->flush();

            return true;
        }

        throw new \Exception(sprintf('User with id %d is not a member of tenant with id %d' , $user->getId(), $tenant->getId()));
    }

    public function removeRoleFromUserInTenant(
        $role,
        MultiTenantUserInterface $user,
        MultiTenantTenantInterface $tenant)
    {
        $tenantUser = $this->tenantUserRepository->findOneBy(
            array(
                'tenant' => $tenant,
                'user' => $user
            )
        );

        if ($tenantUser) {

            if ($tenantUser->hasRole($role)) {
                $tenantUser->removeRole($role);
            }

            $this->entityManager->persist($tenantUser);
            $this->entityManager->flush();

            return true;
        }

        throw new \Exception(sprintf('User with id %d is not a member of tenant with id %d' , $user->getId(), $tenant->getId()));
    }
}
