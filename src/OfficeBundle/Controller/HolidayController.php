<?php

namespace OfficeBundle\Controller;

use OfficeBundle\Entity\Holiday;
use OfficeBundle\Entity\UserPersonal;
use OfficeBundle\Form\HolidayType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HolidayController extends Controller
{
    public function indexAction()
    {
        return $this->render('OfficeBundle:holiday:index.html.twig');
    }

    public function createAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $user = $manager->getRepository(UserPersonal::class)->find($userId);
        $entity = new Holiday();

        $form = $this->createForm(HolidayType::class, $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            $data->setDays($data->getDay()->format('d'));
            $data->setMonth($data->getDay()->format('m'));
            $data->setYear($data->getDay()->format('Y'));
            $data->setInputBy($user);
            $data->setCreatedAt(new \DateTime());

            $manager->persist($data);
            $manager->flush();

            return $this->redirectToRoute('office_holiday_index');
        }

        return $this->render('OfficeBundle:holiday:input.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
