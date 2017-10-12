<?php

namespace OfficeBundle\Controller;

use OfficeBundle\Entity\Shift;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ShiftController extends Controller
{
    public function indexAction()
    {
        $data = $this->getDoctrine()->getManager()->getRepository(Shift::class)->findAll();

        return $this->render('OfficeBundle:shift:index.html.twig', [
            'data' => $data,
        ]);
    }

    public function createAction(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            $data = new Shift();

            $data->setLabel($request->get('keterangan'));
            $data->setStartTime($request->get('jam-masuk'));
            $data->setEndTime($request->get('jam-pulang'));
            $data->setCreatedAt(new \DateTime());

            $manager = $this->getDoctrine()->getManager();

            $manager->persist($data);
            $manager->flush();

            return $this->redirectToRoute('office_admin_shift_index');
        }

        return $this->render('OfficeBundle:shift:form.html.twig', [
            'input_label' => 'Masukkan jam kerja',
        ]);
    }

    public function deleteAction($id)
    {
        $manager = $this->getDoctrine()->getManager();

        $data = $manager->getRepository(Shift::class)->find($id);

        $manager->remove($data);
        $manager->flush();

        return $this->redirectToRoute('office_admin_shift_index');
    }

    public function editAction(Request $request, $id)
    {
        $data = $this->getDoctrine()->getManager()->getRepository(Shift::class)->find($id);

        if ($request->getMethod() === 'POST') {
            $data->setLabel($request->get('keterangan'));
            $data->setStartTime($request->get('jam-masuk'));
            $data->setEndTime($request->get('jam-pulang'));

            $manager = $this->getDoctrine()->getManager();

            $manager->merge($data);
            $manager->flush();

            return $this->redirectToRoute('office_admin_shift_index');
        }

        return $this->render('OfficeBundle:shift:form.html.twig', [
            'data' => $data,
            'input_label' => 'Perbarui data',
        ]);
    }
}
