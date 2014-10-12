<?php

namespace Tahoe\Bundle\MultiTenancyBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Tahoe\Bundle\CrudBundle\Controller\CrudController;
use Tahoe\Bundle\MultiTenancyBundle\Form\UserListType;
use Tahoe\Bundle\MultiTenancyBundle\Model\MultiTenantUserInterface;

/**
 * TenantUser controller.
 *
 */
class TenantUserController extends CrudController
{
    /**
     * Lists all Tenant entities.
     *
     */
    public function indexAction()
    {
        $tenant = $this->container->get('tahoe.multi_tenancy.tenant_resolver')->getTenant();

        $tenantUsers = $this->repository->findByTenant($tenant->getId());

        $invitationForm = $this->createForm('invitation_form', null, array(
                'method' => 'post',
                'action' => $this->generateUrl('invitation_create')
            ));

        $invitations = $this->container->get('invitation_repository')->findAll();

        $form = $this->createActionForm();

        return $this->render(
            'TahoeMultiTenancyBundle:TenantUser:index.html.twig',
            array(
                'tenant' => $tenant,
                'tenantUsers' => $tenantUsers,
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
                'action' => $this->generateUrl('admin_tenant_user_handle_action')
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

            return $this->redirect($this->generateUrl('tenantuser_index'));
        }

        throw new \Exception('Submitted invalid form on tenant user index page');
    }

    /**
     * @param array $userIdRole Must follow the pattern userId => Role, array(1=> 'ROLE_ADMIN')
     */
    protected function promoteUsers($userIdRole) {

        $tenantUserHandler = $this->container->get('tahoe.multi_tenancy.tenant_user_handler');
        $userRepository = $this->container->get('user_repository');

        $tenant = $this->container->get('tahoe.multi_tenancy.tenant_resolver')->getTenant();

        foreach ($userIdRole as $userId => $role) {

            $user = $userRepository->find($userId);
            $tenantUserHandler->addRoleToUserInTenant($role, $user, $tenant);
        }
    }

    /**
     * @param array $userIdRole Must follow the pattern userId => Role, array(1=> 'ROLE_ADMIN')
     */
    protected function demoteUsers($userIdRole) {

        $tenantUserHandler = $this->container->get('tahoe.multi_tenancy.tenant_user_handler');
        $userRepository = $this->container->get('user_repository');

        $tenant = $this->container->get('tahoe.multi_tenancy.tenant_resolver')->getTenant();

        foreach ($userIdRole as $userId => $role) {

            $user = $userRepository->find($userId);
            $tenantUserHandler->removeRoleFromUserInTenant($role, $user, $tenant);
        }
    }

    /**
     * @param array $userIdRole Must follow the pattern userId => ignored, array(1=> 'anything')
     */
    protected function removeUsers($userIdRole) {
        $tenantUserHandler = $this->container->get('tahoe.multi_tenancy.tenant_user_handler');
        $userRepository = $this->container->get('user_repository');

        $tenant = $this->container->get('tahoe.multi_tenancy.tenant_resolver')->getTenant();

        foreach ($userIdRole as $userId => $dummy) {

            $user = $userRepository->find($userId);
            $tenantUserHandler->removeUserFromTenant($user, $tenant);
        }
    }
}
