<?php

namespace WobbleCode\NotificationBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * @Route("/notification")
 */
class NotificationController extends Controller
{
    /**
     * @Route("/read/{id}", name="wobblecode_notification_read")
     * @Method({"PUT", "GET"})
     */
    public function readAction(Request $request, $id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $repo = $dm->getRepository('WobbleCodeNotificationBundle:Notification');
        $notification = $repo->find($id);

        $user = $this->getUser();
        if ($notification->getUser()->getId() != $user->getId()) {
            throw new AccessDeniedHttpException('You can not access this item.');
        }

        $response = new Response();

        $notification->setStatus('read');
        $notification->setReadAt(new \DateTime());

        $dm->persist($notification);
        $dm->flush();

        $response->setStatusCode(200);

        return $response;
    }

    /**
     * @Route("/task/{id}", name="wobblecode_notification_task")
     * @Method({"PUT", "GET"})
     */
    public function taskAction(Request $request, $id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $repo = $dm->getRepository('WobbleCodeNotificationBundle:Notification');
        $notification = $repo->find($id);

        $user = $this->getUser();
        if ($notification->getUser()->getId() != $user->getId()) {
            throw new AccessDeniedHttpException('You can not access this item.');
        }

        if ($notification->getActionStatus() == 'completed') {
            $notification->setActionStatus('pending');
            $notification->setActionAt(null);
            $this->addFlash('success', 'notification.event.pending');
        } else {
            $notification->setActionStatus('completed');
            $notification->setReadAt(new \DateTime());
            $notification->setActionAt(new \DateTime());
            $this->addFlash('success', 'notification.event.completed');
        }

        $dm->persist($notification);
        $dm->flush();

        /**
         * TODO Add bundle setting
         */
        return $this->redirect($this->generateUrl('app_user_notification'));
    }
}
