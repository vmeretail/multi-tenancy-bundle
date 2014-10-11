<?php


namespace Tahoe\Bundle\MultiTenancyBundle\Handler;


use Doctrine\ORM\EntityManager;
use Tahoe\Bundle\MultiTenancyBundle\Factory\OrganizationUserFactory;
use Tahoe\Bundle\MultiTenancyBundle\Model\InvitationInterface;
use Tahoe\Bundle\MultiTenancyBundle\Model\MultiTenantOrganizationInterface;
use Tahoe\Bundle\MultiTenancyBundle\Model\MultiTenantUserInterface;
use Tahoe\Bundle\MultiTenancyBundle\Repository\OrganizationUserRepository;

/**
 * Class OrganizationUserHandler
 *
 * @author Konrad Podgórski <konrad.podgorski@gmail.com>
 */
class OrganizationUserHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;
    /**
     * @var OrganizationUserRepository
     */
    protected $organizationUserRepository;
    /**
     * @var OrganizationUserFactory
     */
    protected $organizationUserFactory;

    function __construct($entityManager, $organizationUserFactory, $organizationUserRepository)
    {
        $this->entityManager = $entityManager;
        $this->organizationUserFactory = $organizationUserFactory;
        $this->organizationUserRepository = $organizationUserRepository;
    }

    /**
     * @param MultiTenantUserInterface         $user
     * @param MultiTenantOrganizationInterface $organization
     * @param array                            $roles
     *
     * @return \Tahoe\Bundle\MultiTenancyBundle\Entity\OrganizationUser
     * @throws \Exception
     */
    public function addUserToOrganization(
        MultiTenantUserInterface $user,
        MultiTenantOrganizationInterface $organization,
        $roles = array()
    ) {
        $organizationUser = $this->organizationUserRepository->findOneBy(
            array(
                'organization' => $organization,
                'user' => $user
            )
        );

        if ($organizationUser) {
            // user is already a member of this organization

            throw new \Exception(sprintf('User is already a member of given organization'));
        }

        $organizationUser = $this->organizationUserFactory->createNew();
        $organizationUser->setUser($user);
        $organizationUser->setOrganization($organization);
        $organizationUser->setRoles($roles);

        $this->entityManager->persist($organizationUser);

        return $organizationUser;
    }

    /**
     * @param MultiTenantUserInterface         $user
     * @param MultiTenantOrganizationInterface $organization
     *
     * @return bool
     * @throws \Exception
     */
    public function removeUserFromOrganization(
        MultiTenantUserInterface $user,
        MultiTenantOrganizationInterface $organization
    ) {
        $organizationUser = $this->organizationUserRepository->findOneBy(
            array(
                'organization' => $organization,
                'user' => $user
            )
        );

        if ($organizationUser) {
            $this->entityManager->remove($organizationUser);
            $this->entityManager->flush();

            return true;
        }

        throw new \Exception(sprintf('User is not a member of given organization'));
    }

    public function addRoleToUserInOrganization(
        $role,
        MultiTenantUserInterface $user,
        MultiTenantOrganizationInterface $organization)
    {
        $organizationUser = $this->organizationUserRepository->findOneBy(
            array(
                'organization' => $organization,
                'user' => $user
            )
        );

        if ($organizationUser) {

            if (false === $organizationUser->hasRole($role)) {
                $organizationUser->addRole($role);
            }

            $this->entityManager->persist($organizationUser);
            $this->entityManager->flush();

            return true;
        }

        throw new \Exception(sprintf('User with id %d is not a member of organization with id %d' , $user->getId(), $organization->getId()));
    }

    public function removeRoleFromUserInOrganization(
        $role,
        MultiTenantUserInterface $user,
        MultiTenantOrganizationInterface $organization)
    {
        $organizationUser = $this->organizationUserRepository->findOneBy(
            array(
                'organization' => $organization,
                'user' => $user
            )
        );

        if ($organizationUser) {

            if ($organizationUser->hasRole($role)) {
                $organizationUser->removeRole($role);
            }

            $this->entityManager->persist($organizationUser);
            $this->entityManager->flush();

            return true;
        }

        throw new \Exception(sprintf('User with id %d is not a member of organization with id %d' , $user->getId(), $organization->getId()));
    }
}
