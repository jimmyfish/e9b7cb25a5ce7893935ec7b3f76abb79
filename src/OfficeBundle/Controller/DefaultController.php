<?php

namespace OfficeBundle\Controller;

use OfficeBundle\Entity\CompanyProfile;
use OfficeBundle\Entity\UserPersonal;
use Symfony\Bridge\Doctrine\Tests\Fixtures\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('OfficeBundle:home:index.html.twig');
    }

    public function createUserAction(Request $request)
    {
        if ($request->getMethod() == 'GET') {
            return $this->render('');
        }

        if ($request->getMethod() == 'POST') {
            $em = $this->getDoctrine()->getManager();

            $user = new UserPersonal();

            $test = $user->createUser($request);

            $em->persist($test);
            $em->flush();

            return $this->redirect('/list-user');
        }

        return true;
    }

    public function deleteUserAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $test = $em->getRepository(UserPersonal::class)->findById($id);

        $em->remove($test);
        $em->flush();

        return $this->redirect('/list-user');
    }

    public function updateUserAction(Request $request, $id)
    {
        if ($request->getMethod() == 'POST') {
            $em = $this->getDoctrine()->getManager();

            $test = $em->getRepository(UserPersonal::class)->findById($id);

            $nama = $request->get('nama');
            $email = $request->get('email');
            $password = $request->get('password');
            $tempatLahir = $request->get('tempat-lahir');
            $tanggalLahir = $request->get('tanggal-lahir');
            $noTelp = $request->get('no-telp');
            $noKtp = $request->get('no-ktp');

            if ($test instanceof UserPersonal) {
                $test->setNama($nama);
                $test->setEmail($email);
                $test->setPassword($password);
                $test->setTempatLahir($tempatLahir);
                $test->setTanggalLahir($tanggalLahir);
                $test->setNoTelp($noTelp);
                $test->setNoKtp($noKtp);
            }

            $em->flush();

            return $this->redirect('/list-user');
        }

        return 'OK';
    }

    public function showAction()
    {
        $em = $this->getDoctrine()->getManager();

        $test = $em->getRepository(UserPersonal::class)->findAll();

        $encoders = array(new JsonEncoder());
        $normalizer = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizer, $encoders);

        $jsonContent = $serializer->serialize($test, 'json');

        $response = new Response($jsonContent);

        return $response;
    }

    public function loginAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $username = $request->get('username');
            $password = md5($request->get('password'));
            $em = $this->getDoctrine()->getManager();
            $data = $em->getRepository(UserPersonal::class)->findByUsername($username);

            if ($data instanceof UserPersonal) {
                if ($data != null) {
                    if ($password == $data->getPassword()) {
                        $session = $request->getSession();

                        $session->set('username', ['value' => $username]);
                        $session->set('nama', ['value' => $data->getNama()]);

                        return 'ini dashboard';
                    } else {
                        return 'login kembali';
                    }
                } else {
                    return 'check username';
                }
            }
        }

        return $this->render('OfficeBundle:Default:login.html.twig');
    }

    public function logoutAction(Request $request)
    {
        $session = $request->getSession();
        $session->clear();

        return 'berhasil logout';
    }

    public function createCompanyProfileAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $company = new CompanyProfile();
        $asem = $company->createCompany($request);

        $em->persist($asem);
        $em->flush();

        return $this->redirect('/list-company');
    }

    public function companyAction()
    {
        $em = $this->getDoctrine()->getManager();

        $test = $em->getRepository(CompanyProfile::class)->findAll();

        $encoders = array(new JsonEncoder());
        $normalizer = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizer, $encoders);

        $jsonContent = $serializer->serialize($test, 'json');

        $response = new Response($jsonContent);

        return $response;
    }

    public function deleteCompanyAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $test = $em->getRepository(CompanyProfile::class)->findById($id);

        $em->remove($test);
        $em->flush();

        return $this->redirect('/list-company');
    }

    public function testAction()
    {
        return $this->render('OfficeBundle:home:index.html.twig');
    }
}
