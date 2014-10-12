<?php


namespace Tahoe\Bundle\MultiTenancyBundle\Handler;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Tahoe\Bundle\MultiTenancyBundle\Factory\InvitationFactory;
use Tahoe\Bundle\MultiTenancyBundle\Model\InvitationInterface;
use Tahoe\Bundle\MultiTenancyBundle\Repository\InvitationRepository;

/**
 * Class InvitationHandler
 *
 * @author Konrad Podgórski <konrad.podgorski@gmail.com>
 */
class InvitationHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;
    /**
     * @var InvitationFactory
     */
    protected $invitationFactory;
    /**
     * @var InvitationRepository
     */
    protected $invitationRepository;
    /**
     * @var EntityRepository
     */
    protected $userRepository;
    /**
     * @var TenantUserHandler
     */
    protected $tenantUserHandler;

    /**
     * @param $entityManager
     * @param $invitationFactory
     * @param $invitationRepository
     * @param $tenantUserHandler
     * @param $userRepository
     */
    function __construct(
        $entityManager,
        $invitationFactory,
        $invitationRepository,
        $tenantUserHandler,
        $userRepository
    ) {
        $this->entityManager = $entityManager;
        $this->invitationFactory = $invitationFactory;
        $this->invitationRepository = $invitationRepository;
        $this->tenantUserHandler = $tenantUserHandler;
        $this->userRepository = $userRepository;
    }

    public function acceptInvitationById($invitationId)
    {
        $invitation = $this->invitationRepository->find($invitationId);

        if ($invitation === null) {
            throw new \Exception(sprintf(
                'Invitation with id %d doesn\'t exist. Maybe it\'s from different tenant?',
                $invitationId
            ));
        }

        return $this->acceptInvitation($invitation);
    }

    /**
     * @param InvitationInterface $invitation
     *
     * @return \Tahoe\Bundle\MultiTenancyBundle\Entity\TenantUser
     */
    public function acceptInvitation(InvitationInterface $invitation)
    {
        $user = $this->userRepository->findOneBy(array('emailCanonical' => $invitation->getEmail()));

        $tenant = $invitation->getTenant();

        $tenantUser = $this->tenantUserHandler->addUserToTenant($user, $tenant);

        $this->entityManager->flush();

        $this->delete($invitation, true);

        return $tenantUser;
    }

    public function delete($invitation, $withFlush = false)
    {
        $this->entityManager->remove($invitation);

        if ($withFlush) {
            $this->entityManager->flush();
        }
    }

    public function rejectInvitationById($invitationId)
    {
        $invitation = $this->invitationRepository->find($invitationId);

        if ($invitation === null) {
            throw new \Exception(sprintf(
                'Invitation with id %d doesn\'t exist. Maybe it\'s from different tenant?',
                $invitationId
            ));
        }

        return $this->rejectInvitation($invitation);
    }

    public function rejectInvitation(InvitationInterface $invitation)
    {
        $this->delete($invitation, true);

        return true;
    }
}