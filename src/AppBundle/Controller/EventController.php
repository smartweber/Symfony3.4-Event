<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Event;
use AppBundle\Form\CallEventFormType;
use AppBundle\Form\MeetingEventFormType;

class EventController extends Controller
{
    /**
     * @Route("/event", name="event_list")
     */
    public function listAction()
    {
        $rep = $this->getDoctrine()->getRepository(Event::class);
        $eventList = $rep->findBy([], ['createdDate' => 'DESC']);
        return $this->render('event/list.html.twig', [
            'event_list' => $eventList,
        ]);
    }

    /**
     * @param Request $request
     * @Route("/event/create_call_event", name="create_call_event")
     */
	public function createCallEventAction(Request $request, \Swift_Mailer $mailer)
    {
        $event = new Event();
        $form = $this->createForm(CallEventFormType::class, $event);


        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $event->setCreatedDate(new \DateTime());
            $participants = explode(PHP_EOL, $event->getParticipants());
            if (count($participants) !== 2) {
                $this->addFlash('success', 'The participants should be 2.');
            } else {
                $valid = true;
                for ($index = 0; $index < 2; $index++) {
                    if (!filter_var($participants[$index], FILTER_VALIDATE_EMAIL)) {
                        $valid = false;
                        break;
                    }       
                }

                if (!$valid) {
                    $this->addFlash('success', 'Every participant email should be valid.');
                } else {
                    for ($index = 0; $index < 2; $index++) {
                        $this->sendMail($mailer, $participants[$index], 'Call Event Creation', 'Call Event is created for you.');
                    }

                    try {
                        $event->setParticipants($participants);
                        $em->persist($event);
                        $em->flush();
                        $this->addFlash('success', 'Event created!');
                        return $this->redirectToRoute('event_list');
                    } catch(\Exception $e){
                        $this->addFlash('success', $e->getMessage());
                    }
                }
            }
        }

        return $this->render('event/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @Route("/event/create_meeting_event", name="create_meeting_event")
     */
	public function createMeetingEventAction(Request $request)
    {
        $event = new Event();
        $form = $this->createForm(MeetingEventFormType::class, $event);


        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $event->setCreatedDate(new \DateTime());
            $participants = explode(PHP_EOL, $event->getParticipants());
            if (count($participants) !== 3) {
                $this->addFlash('success', 'The participants should be 3.');
            } else {
                try {
                    $event->setParticipants($participants);
                    $em->persist($event);
                    $em->flush();
                    $this->addFlash('success', 'Event created!');
                    return $this->redirectToRoute('event_list');
                } catch(\Exception $e){
                    $this->addFlash('success', $e->getMessage());
                }
            }
        }

        return $this->render('event/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request  $request
     * @param Event $event
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/event/call/{event}", name="edit_call_event")
     */
    public function editCallEventAction(Request $request, Event $event)
    {
        $event->setParticipants(implode(PHP_EOL, $event->getParticipants()));
        $form = $this->createForm(CallEventFormType::class, $event);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $participants = explode(PHP_EOL, $event->getParticipants());
            if (count($participants) !== 2) {
                $this->addFlash('success', 'The participants should be 2.');
            } else {
                $valid = true;
                for ($index = 0; $index < 2; $index++) {
                    if (!filter_var($participants[$index], FILTER_VALIDATE_EMAIL)) {
                        $valid = false;
                        break;
                    }       
                }

                if (!$valid) {
                    $this->addFlash('success', 'Every participant email should be valid.');
                } else {
                    try {
                        $event->setParticipants($participants);
                        $em->persist($event);
                        $em->flush();
                        $this->addFlash('success', 'Event edited!');
                        return $this->redirectToRoute('event_list');
                    } catch(\Exception $e){
                        $this->addFlash('success', $e->getMessage());
                    }
                }
            }
        }
        return $this->render('event/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request  $request
     * @param Event $event
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/event/meeting/{event}", name="edit_meeting_event")
     */
    public function editMeetingEventAction(Request $request, Event $event)
    {
        
        $event->setParticipants(implode(PHP_EOL, $event->getParticipants()));
        $form = $this->createForm(MeetingEventFormType::class, $event);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $participants = explode(PHP_EOL, $event->getParticipants());
            if (count($participants) !== 3) {
                $this->addFlash('success', 'The participants should be 3.');
            } else {
                try {
                    $event->setParticipants($participants);
                    $em->persist($event);
                    $em->flush();
                    $this->addFlash('success', 'Event edited!');
                    return $this->redirectToRoute('event_list');
                } catch(\Exception $e){
                    $this->addFlash('success', $e->getMessage());
                }
            }
        }
        return $this->render('event/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request  $request
     * @param Event $event
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/delete/call/{event}", name="delete_call_event")
     */
    public function deleteCallEventAction(Request $request, Event $event)
    {
        if ($event === null) {
            return $this->redirectToRoute('event_list');
        }
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($event);
            $em->flush();
            return $this->redirectToRoute('event_list');
        } catch(\Exception $e){
            $this->addFlash('success', $e->getMessage());
        }
    }

    /**
     * @param Request  $request
     * @param Event $event
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/delete/meeting/{event}", name="delete_meeting_event")
     */
    public function deleteMeetingCallAction(Request $request, Event $event)
    {
        if ($event === null) {
            return $this->redirectToRoute('event_list');
        }
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($event);
            $em->flush();
            return $this->redirectToRoute('event_list');
        } catch(\Exception $e){
            $this->addFlash('success', $e->getMessage());
        }
    }

    public function sendMail( \Swift_Mailer $mailer, $sendTo, $title, $meg) {
        $message = (new \Swift_Message($title))
        ->setFrom('no-reply@test.com')
        ->setTo($sendTo)
        ->setBody(
            $this->renderView(
                // app/Resources/views/emails/event.html.twig
                'emails/event.html.twig',
                array('meg' => $meg)
            ),
            'text/html'
        );
        $mailer->send($message);
    }
}