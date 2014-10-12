<?php


namespace Tahoe\Bundle\MultiTenancyBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class InvitationController extends Controller
{
    public function pendingAction()
    {
        $filters = $this->getDoctrine()->getManager()->getFilters();

        // for a moment we need to disable tenant aware filter to fetch invitations from all tenants
        $filters->disable("tenantAware");
        $invitations = $this->container->get('invitation_repository')->findBy(array(
                'email' => $this->getUser()->getEmailCanonical()
            ));
        $filters->enable("tenantAware");

        return $this->render('@TahoeMultiTenancy/Invitation/index.html.twig', array('invitations' => $invitations));
    }

    public function actionAction(Request $request)
    {

        $action = $request->get('invitation');

        $invitationHandler = $this->container->get('tahoe.multi_tenancy.invitation_handler');

        $filters = $this->getDoctrine()->getManager()->getFilters();

        // for a moment we need to disable tenant aware filter to fetch invitations from all tenants
        $filters->disable("tenantAware");

        if (isset($action['accept'])) {
            $invitationId = $action['accept'];
            $invitationHandler->acceptInvitationById($invitationId);

        }

        if (isset($action['reject'])) {
            $invitationId = $action['reject'];
            $invitationHandler->rejectInvitationById($invitationId);
        }
        $filters->enable("tenantAware");

        return $this->redirect($this->generateUrl('pending_invitation'));

    }
}