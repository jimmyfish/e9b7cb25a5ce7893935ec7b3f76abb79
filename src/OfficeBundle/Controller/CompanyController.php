<?php

namespace OfficeBundle\Controller;

use OfficeBundle\Entity\CompanyProfile;
use OfficeBundle\Form\CompanyProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CompanyController extends Controller
{
    public function createAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $test = new CompanyProfile();
            $data = $test->createCompany($request);

            $em = $this->getDoctrine()->getEntityManager();

            $em->persist($data);
            $em->flush();

            return $this->redirect($this->generateUrl('office_show_company'));
        }
//        $form = $this->createForm(CompanyProfileType::class);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em = $this->getDoctrine()->getEntityManager();
//
//            $em->persist($form->getData());
//            $em->flush();
//
//            return $this->redirect($this->generateUrl('office_company_create'));
//        }

        return $this->render('OfficeBundle:company:create.html.twig');
    }

    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(CompanyProfile::class)->findById($id);

        $em->remove($data);

        $em->flush();

        return $this->redirect($this->generateUrl('office_show_company'));
    }

    public function listCompanyAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(CompanyProfile::class)->findAll();

        return $this->render('OfficeBundle:company:list.html.twig', ['data' => $data]);
    }

    public function updateCompanyAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(CompanyProfile::class)->findById($id);

        if ($data instanceof CompanyProfile) {
            if ($request->getMethod() == 'POST') {
                $data->setNamaPerusahaan($request->get('nama-perusahaan'));

                $em->persist($data);
                $em->flush();

                return $this->redirect($this->generateUrl('office_show_company'));
            }
        }

        return $this->render('OfficeBundle:company:update-company.html.twig', ['data' => $data]);
    }
}
