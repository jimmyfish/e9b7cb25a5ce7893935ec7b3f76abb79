<?php

namespace OfficeBundle\Controller;

use OfficeBundle\Entity\CompanyProfile;
use OfficeBundle\Entity\Device;
use OfficeBundle\Entity\Fingerprint;
use OfficeBundle\Entity\UserPersonal;
use OfficeBundle\Form\DeviceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class FingerprintController extends Controller
{
    public function deviceListAction()
    {
        $data = $this->getDoctrine()->getManager()->getRepository(Device::class)->findAll();

        return $this->render('OfficeBundle:fingerprint:device-list.html.twig', [
            'data' => $data,
        ]);
    }

    public function deviceAddAction(Request $request)
    {
        $form = $this->createForm(DeviceType::class);
        $form->handleRequest($request);

        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirect($request->headers->get('referer'));
        }

        if ($form->isValid()) {
            $data = $form->getData();

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($data);
            $manager->flush();

            return $this->redirectToRoute('office_admin_fingerprint_device_list');
        }

        return $this->render('OfficeBundle:fingerprint:device-add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function deviceEditAction(Request $request,$id)
    {
        $manager = $this->getDoctrine()->getManager();

        $data = $manager->getRepository(Device::class)->find($id);

        if($request->getMethod() == 'POST')
        {
            if($data instanceof Device) {
                $data->setDeviceName($request->get('device-name'));
                $data->setSn($request->get('sn'));
                $data->setVc($request->get('vc'));
                $data->setAc($request->get('ac'));
                $data->setVkey($request->get('vkey'));
            }
            $manager->persist($data);
            $manager->flush();

            return $this->redirect($this->generateUrl('office_admin_fingerprint_device_list'));
        }

        return $this->render('OfficeBundle:fingerprint:device-edit.html.twig',[
            'data' => $data
        ]);
    }

    public function userListAction(Request $request)
    {
        $loginUser = $this->get('security.token_storage')->getToken()->getUser();

        $data = '';
        if ($this->isGranted('ROLE_ADMIN')) {
            $data = $this->getDoctrine()->getManager()->getRepository(UserPersonal::class)->findAll();
        } elseif ($this->isGranted('ROLE_VALIDATOR')) {
            $penempatan = $loginUser->getPenempatan();

            if ($penempatan instanceof CompanyProfile) {
                $data = $this->getDoctrine()->getManager()->getRepository(UserPersonal::class)->findBy([
                    'penempatan' => $loginUser->getPenempatan()->getId(),
                ]);
            } else {
                $data = null;
            }
        }

        if (count($data) > 0) {
            foreach ($data as $item) {
                $url = 'http://'.$request->headers->get('host').$this->generateUrl('office_fingerprint_register', ['id' => $item->getId()]);
                $urlDecoded = base64_encode($url);
                $item->setUrl($urlDecoded);
            }
        }

        return $this->render('OfficeBundle:fingerprint:user-list.html.twig', [
            'data' => $data,
        ]);
    }

    public function registerAction(Request $request)
    {
        $id = $request->get('id');
        $processUrl = 'http://'.$request->headers->get('host').$this->generateUrl('office_fingerprint_register_process');
        $getAcUrl = 'http://'.$request->headers->get('host').$this->generateUrl('office_fingerprint_get_activation');

        echo "$id;SecurityKey;15;".$processUrl.';'.$getAcUrl;
    }

    public function registrationProcessAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        if (isset($_POST['RegTemp']) && !empty($_POST['RegTemp'])) {
            $data = explode(';', $_POST['RegTemp']);

            $vStamp = $data[0];
            $sn = $data[1];
            $user_id = $data[2];
            $regTemp = $data[3];

            $device = $manager->getRepository(Device::class)->findOneBy(['sn' => $sn]);
            $salt = md5($device->getAc().$device->getVkey().$regTemp.$sn.$user_id);

            // if (strtoupper($vStamp) == strtoupper($salt)) {
            $fid = $manager->createQuery('SELECT e FROM OfficeBundle:Fingerprint e where e.userId = :id')
            ->setParameter('id', $user_id)->getArrayResult();

            if (0 == count($fid)) {
                $user = $manager->getRepository(UserPersonal::class)->find($user_id);

                $data = new Fingerprint();

                $data->setUserId($user);
                $data->setFingerStatus(count($fid) + 1);
                $data->setFingerData($regTemp);
                $data->setFingerSalt($salt);

                $manager->persist($data);
                $manager->flush();

                $res['result'] = true;
            } else {
                echo 'Parameter invalid..';
            }
        }
    }

    public function getActivationCodeAction(Request $request)
    {
        $vc = $request->query->get('vc');

        $data = $this->getDoctrine()->getManager()
                        ->createQuery('SELECT e FROM OfficeBundle:Device e where e.vc = :vc')
                        ->setParameter('vc', $vc)->getArrayResult()[0];

        echo $data['ac'].$data['sn'];
    }

    public function presenceAction(Request $request)
    {
    }

    public function presenceProcessAction(Request $request, $username)
    {
        $data = $this->getDoctrine()->getManager()->getRepository(UserPersonal::class)->findOneBy(['username' => $username]);
        $finger = $data->getFinger();
        $id = $data->getId();
        $verifyUrl = 'http://'.$request->headers->get('host').$this->generateUrl('fingertest_finger_verifyProcess');
        $getAcUrl = 'http://'.$request->headers->get('host').$this->generateUrl('fingertest_finger_get_ac');

        echo 'SecurityKey;10;'.$verifyUrl.';'.$getAcUrl.';extraParams';
    }

    public function dummyAction(Request $request)
    {
        $id = $request->get('id');
        $processUrl = 'http://'.$request->headers->get('host').$this->generateUrl('office_fingerprint_register_process');
        $getAcUrl = 'http://'.$request->headers->get('host').$this->generateUrl('office_fingerprint_get_activation');

        echo "$id;SecurityKey;15;".$processUrl.';'.$getAcUrl;
    }

    public function deleteUserAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
	$data = $manager->getRepository(Fingerprint::class)->findOneBy(['userId' => $request->get('user_id')]);
       // $data = $manager->getRepository(Fingerprint::class)->find($request->get('user_id'));

        if ($data instanceof Fingerprint) {
            $manager->remove($data);
            $manager->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    public function deleteDeviceAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(Device::class)->find($id);

//        Request::setTrustedProxies(array('127.0.0.1'));

        $dataArray = [
            'ip' => $this->container->get('request_stack')->getCurrentRequest()->getClientIp(),
            'data_fingerprint' => serialize($data)
        ];

        $logger = $this->get('logger');
        $logger->err(json_encode($dataArray));

        $em->remove($data);
        $em->flush();

        return $this->redirectToRoute('office_admin_fingerprint_device_list');
    }
}
