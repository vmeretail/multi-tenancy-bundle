<?php

namespace Tahoe\Bundle\MultiTenancyBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Tahoe\Bundle\CrudBundle\Controller\CrudController;
use Tahoe\Bundle\MultiTenancyBundle\Form\UserListType;
use Tahoe\Bundle\MultiTenancyBundle\Model\MultiTenantUserInterface;

/**
 * OrganizationUser controller.
 *
 */
class OrganizationUserController extends CrudController
{
    /**
     * Lists all Organization entities.
     *
     */
    public function indexAction()
    {
        $organization = $this->container->get('tahoe.multi_tenancy.organization_resolver')->getOrganization();

        $organizationUsers = $this->repository->findByOrganization($organization->getId());

        $invitationForm = $this->createForm('invitation_form', null, array(
                'method' => 'post',
                'action' => $this->generateUrl('invitation_create')
            ));

        $invitations = $this->container->get('invitation_repository')->findAll();

        $form = $this->createActionForm();

        return $this->render(
            'TahoeMultiTenancyBundle:OrganizationUser:index.html.twig',
            array(
                'organization' => $organization,
                'organizationUsers' => $organizationUsers,
                'invitationForm' => $invitationForm->createView(),
                'invitations' => $invitations,
                'form' => $form->createView()
            )
        );
    }

    protected function createActionForm()
    {
        return  $this->createForm(new UserListType(), array(

            ), array(
                'method' => 'post',
                'action' => $this->generateUrl('admin_organization_user_handle_action')
            ));
    }

    public function handleActionAction(Request $request)
    {
        $form = $this->createActionForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $data = $form->getData();

            if (is_array($data['promote'])) {
                $this->promoteUsers($data['promote']);
            }

            if (is_array($data['demote'])) {
                $this->demoteUsers($data['demote']);
            }

            if (is_array($data['remove'])) {
                $this->removeUsers($data['remove']);
            }

            return $this->redirect($this->generateUrl('organizationuser_index'));
        }

        throw new \Exception('Submitted invalid form on organization user index page');
    }

    /**
     * @param array $userIdRole Must follow the pattern userId => Role, array(1=> 'ROLE_ADMIN')
     */
    protected function promoteUsers($userIdRole) {

        $organizationUserHandler = $this->container->get('tahoe.multi_tenancy.organization_user_handler');
        $userRepository = $this->container->get('user_repository');

        $organization = $this->container->get('tahoe.multi_tenancy.organization_resolver')->getOrganization();

        foreach ($userIdRole as $userId => $role) {

            $user = $userRepository->find($userId);
            $organizationUserHandler->addRoleToUserInOrganization($role, $user, $organization);
        }
    }

    /**
     * @param array $userIdRole Must follow the pattern userId => Role, array(1=> 'ROLE_ADMIN')
     */
    protected function demoteUsers($userIdRole) {

        $organizationUserHandler = $this->container->get('tahoe.multi_tenancy.organization_user_handler');
        $userRepository = $this->container->get('user_repository');

        $organization = $this->container->get('tahoe.multi_tenancy.organization_resolver')->getOrganization();

        foreach ($userIdRole as $userId => $role) {

            $user = $userRepository->find($userId);
            $organizationUserHandler->removeRoleFromUserInOrganization($role, $user, $organization);
        }
    }

    /**
     * @param array $userIdRole Must follow the pattern userId => ignored, array(1=> 'anything')
     */
    protected function removeUsers($userIdRole) {
        $organizationUserHandler = $this->container->get('tahoe.multi_tenancy.organization_user_handler');
        $userRepository = $this->container->get('user_repository');

        $organization = $this->container->get('tahoe.multi_tenancy.organization_resolver')->getOrganization();

        foreach ($userIdRole as $userId => $dummy) {

            $user = $userRepository->find($userId);
            $organizationUserHandler->removeUserFromOrganization($user, $organization);
        }
    }
}
