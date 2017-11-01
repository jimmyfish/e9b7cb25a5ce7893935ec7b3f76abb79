<?php

namespace OfficeBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\Debug;
use DoctrineExtensions\Query\Sqlite\Day;
use OfficeBundle\Entity\Cuti;
use OfficeBundle\Entity\DayType;
use OfficeBundle\Entity\Holiday;
use OfficeBundle\Entity\UserJob;
use OfficeBundle\Entity\UserPersonal;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Acl\Exception\Exception;

class DayOffController extends Controller
{
    public function dayOffInputAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $userdata = $this->get('security.token_storage')->getToken()->getUser();

        if ($userdata->getStatus() < 2) {
            $this->get('session')->getFlashBag()->add('message_failure', 'Status karyawan anda belum mencukupi untuk melakukan permintaan cuti');

            return $this->redirectToRoute('office_user_success', [
                'proc_title' => 'Permintaan Cuti',
            ]);
        }

        $type = $manager->getRepository(DayType::class)->findBy(['isDeleted' => 0]);

        $quotas = $userdata->getJob()->getQuotas();

        $dateNow = new \DateTime();

        $monthNow = $dateNow->format('m');
        
        $desireDate = new \DateTime($request->get('date-start'));

        if ($desireDate->format('m') <= 6) {
            $dataCuti = $manager->createQueryBuilder()
                ->select('c')
                ->from('OfficeBundle:Cuti', 'c')
                ->where('c.absDate BETWEEN :firstMonth AND :secondMonth')
                ->andWhere('c.userId = :user')
                ->andWhere('c.isValidated = 0 OR c.isValidated = 1')
                ->andWhere('c.type is null')
                ->setParameter('firstMonth', $desireDate->format('Y') . '-01-01')
                ->setParameter('secondMonth', $desireDate->format('Y') . '-06-30')
                ->setParameter('user', $userdata);

            $quotas = $quotas - count($dataCuti->getQuery()->getResult());
        } else {
            $dataCuti = $manager->createQueryBuilder()
                ->select('c')
                ->from('OfficeBundle:Cuti', 'c')
                ->where('c.absDate BETWEEN :firstMonth AND :secondMonth')
                ->andWhere('c.userId = :user')
                ->andWhere('c.type is null')
                ->andWhere('c.isValidated = 0 OR c.isValidated = 1')
                ->setParameter('firstMonth', $desireDate->format('Y') . '-07-01')
                ->setParameter('secondMonth', $desireDate->format('Y') . '-12-31')
                ->setParameter('user', $userdata);

            $quotas = $quotas - count($dataCuti->getQuery()->getResult());
        }

        if ($userdata == null) {
            throw new \Exception('Error Processing Request', 1);
        }

        if ($request->getMethod() === 'POST') {

            /**
            * Checking if the day interval is not below 14.
            */
            $dateGiven = \DateTime::createFromFormat('d-m-Y', $request->get('date-start'));
            $desireDateDiff = $dateNow->diff($dateGiven);
            $desireDateFormat = $desireDateDiff->format("%a");

            if ($desireDateFormat <= 14) {
                $this->get('session')->getFlashBag()->add('message_failure', 'Hari yang dipilih haruslah berada pada H-14, jika terdapat kepentingan mendesak harap menghubungi admin');

                return $this->redirect($request->headers->get('referer'));
            }

            /**
            * If user has suficient quotas.
            */
            if ($quotas > 0) {
                if ($request->get('day-type') == 0) {
                    /*
                     * Do Sequence for regular day-off.
                     */

                    $data = new Cuti();
                    $data->setUserId($userdata);
                    $givenDate = \DateTime::createFromFormat('d-m-Y', $request->get('date-start'));

                    $data->setTanggal($givenDate->format('d'));
                    $data->setBulan($givenDate->format('m'));
                    $data->setTahun($givenDate->format('Y'));
                    $data->setAbsDate($givenDate);
                    $data->setDescription($request->get('description'));
                    $data->setIsValidated(0);
                    $data->setHashDate($userdata, $request->get('date-start'));
                    $data->setDayGroup(uniqid());

                    $manager->persist($data);
                } else {
                    $cutiType = $manager->getRepository(DayType::class)->find($request->get('day-type'));


                    $manager = $this->getDoctrine()->getManager();
                    $userdata = $this->get('security.token_storage')->getToken()->getUser();

                    $dateStart = \DateTime::createFromFormat('d-m-Y', $request->get('date-start'));

                    $negativeInterval = new \DateInterval('P1D'); // TELL SYSTEM TO BACKWARD 1 DAY
                    $negativeInterval->invert = 1;
                    $dateStart->add($negativeInterval);

                    $newAdd = [];
                    $cutiRaw = $manager->getRepository(Cuti::class)->findAll();
                    $grId = uniqid();

                    for ($i = 0; $i <= $cutiType->getCount(); $i++) {
                        $dateStart->add(new \DateInterval('P1D'));
                        $day = $dateStart->format('d');
                        $month = $dateStart->format('m');
                        $year = $dateStart->format('Y');
                        $abs = $day . '-' . $month . '-' . $year;

                        $data = new Cuti();
                        $data->setUserId($userdata);
                        $data->setTanggal($day);
                        $data->setBulan($month);
                        $data->setTahun($year);
                        $data->setAbsDate(new \DateTime($abs));
                        $data->setHashDate($userdata, $abs);
                        $data->setDescription($cutiType->getName());
                        $data->setDayGroup($grId);
                        $data->setType($cutiType);

                        array_push($newAdd, $data);
                    }

                    // DO THE HARLEMSHAKE

                    foreach ($newAdd as $key => $item) {
                        foreach ($cutiRaw as $keyCuti => $itemCuti) {
                            if ($itemCuti->getHashDate() == $item->getHashDate()) {
                                unset($newAdd[$key]);
                            }
                        }
                    }

                    foreach ($newAdd as $data) {
                        $manager->persist($data);
                    }
                }
            } else {
                $this->get('session')->getFlashBag()->add('message_failure', 'Jatah cuti anda telah habis, sayang sekali.');
                return $this->redirectToRoute('office_user_success', [
                    'proc_title' => 'Permintaan Cuti',
                ]);
            }

            /*
             * TRY TO FLUSH DATA TO DATABASE.
             */

            try {
                $manager->flush();

                $this->get('session')->getFlashBag()->add('message_success', 'Permintaan cuti telah berhasil dimasukkan dan akan dilanjutkan ke ADMIN');
            } catch (UniqueConstraintViolationException $e) {
                $this->get('session')->getFlashBag()->add('message_failure', 'Anda telah mengajukan cuti untuk tanggal tersebut, atau salah satu dari tanggal yang anda pilih sudah pernah diajukan, permintaan ditolak.');
            }

            return $this->redirectToRoute('office_user_success', [
                'proc_title' => 'Permintaan Cuti',
            ]);
        }

        return $this->render('OfficeBundle:user:dayoff-input.html.twig', [
            'user' => $userdata,
            'quotas' => $quotas,
            'type' => $type,
        ]);
    }

    public function dayOffListAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $data = $manager->getRepository(Cuti::class)->findAll();

        return $this->render('OfficeBundle:user:dayoff-list.html.twig', [
            'data' => $data,
        ]);
    }

    public function detailDayAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        $data['cuti'] = $manager->getRepository(Cuti::class)->findBy([
            'userId' => $request->get('user_id'),
        ]);

        $data['user'] = $manager->getRepository(UserPersonal::class)->find($request->get('user_id'));

        return $this->render('OfficeBundle:admin:detail-dayoff.html.twig', [
            'data' => $data,
        ]);
    }

    public function approveDayAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $data = $manager->getRepository(Cuti::class)->find($request->get('cuti_id'));

        $data->setIsValidated(1);
        $data->setValidatedBy($user);

        $manager->merge($data);
        $manager->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    public function rejectDayAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $data = $manager->getRepository(Cuti::class)->find($request->get('cuti_id'));

        if ($data instanceof Cuti) {
            $data->setIsValidated(2);
            $data->setValidatedBy($user);
        }

        $manager->merge($data);
        $manager->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    public function deleteDayAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $data = $manager->getRepository(Cuti::class)->find($request->get('cuti_id'));

        if ((($this->isGranted('ROLE_ADMIN')) or ($data->getUserId()->getId() == $user->getId())) and ($data->getIsValidated() != 1)) {
            $manager->remove($data);
            $manager->flush();

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->redirect($request->headers->get('referer'));
    }

    public function inputDayTypeAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        if ($request->getMethod() === 'POST') {
            $data = new DayType();

            $dayName = ucwords($request->get('day-type'));

            $data->setName($dayName);
            $data->setCount($request->get('day-long'));

            $manager->persist($data);
            $manager->flush();

            return $this->render($this->generateUrl('office_admin_dayoff_type_list'));
        }

        return $this->render('OfficeBundle:admin:input-day-type.html.twig', ['btn_name' => 'Input Jenis Hari']);
    }

    public function formApplicationListAction()
    {
        $manager = $this->getDoctrine()->getManager();

        $dayOff = $manager->createQueryBuilder()
            ->select('user')
            ->from('OfficeBundle:UserPersonal', 'user')
            ->innerJoin('OfficeBundle:Cuti', 'cuti')
            ->where('user.id = cuti.userId');

        return $this->render('OfficeBundle:dayoff:application-list.html.twig', [
            'dayOff' => $dayOff->getQuery()->getResult(),
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function inputQuotasAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        $data['user'] = $manager->getRepository(UserPersonal::class)->findAll();

        if ($request->getMethod() === 'POST') {
            $data = $request->get('desire-user');

            foreach ($data as $item) {
                $userJob = $manager->getRepository(UserJob::class)->findOneBy(['userId' => $item]);

                $userJob->setQuotas($request->get('desire-quotas'));

                $manager->persist($userJob);
            }

            $manager->flush();

            return $this->redirectToRoute('office_admin_dayoff_input_quotas');
        }

        return $this->render('OfficeBundle:dayoff:input-quotas.html.twig', [
            'data' => $data,
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function inputSpecialAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        $data['user'] = $manager->getRepository(UserPersonal::class)->findAll();
        $loginUser = $this->get('security.token_storage')->getToken()->getUser();

        if ($request->getMethod() === 'POST') {
            $dataCutiOld = $manager->getRepository(Cuti::class)->findAll();

            $newCuti = [];
            $userList = [];

            foreach ($request->get('desire-user') as $item) {
                $userdata = $manager->getRepository(UserPersonal::class)->find($item);

                if ($userdata->getStatus() > 1) {
                    array_push($userList, $item);
                }
            }

            $desireDate = new \DateTime($request->get('date-start'));

            /**
             * Checking which user has available quotas
             */
            foreach ($userList as $keyUser => $user) {
                $userdata = $manager->getRepository(UserPersonal::class)->find($user);

                $quotas = $userdata->getJob()->getQuotas();
                $grId = uniqid();

                $dataCuti = $manager->createQueryBuilder()
                    ->select('c')
                    ->from('OfficeBundle:Cuti', 'c')
                    ->where('c.absDate BETWEEN :firstMonth AND :secondMonth')
                    ->andWhere('c.userId = :user')
                    ->andWhere('c.isValidated = 0 OR c.isValidated = 1')
                    ->andWhere('c.type is null')
                    ->setParameter('user', $userdata);

                if ($desireDate->format('m') <= 6) {
                    $dataCuti
                        ->setParameter('firstMonth', $desireDate->format('Y') . '-01-01')
                        ->setParameter('secondMonth', $desireDate->format('Y') . '-06-30');

                    $quotas = $quotas - count($dataCuti->getQuery()->getResult());
                } else {
                    $dataCuti
                        ->setParameter('firstMonth', $desireDate->format('Y') . '-07-01')
                        ->setParameter('secondMonth', $desireDate->format('Y') . '-12-31');

                    $quotas = $quotas - count($dataCuti->getQuery()->getResult());
                }

                if ($quotas <= 0) {
                    unset($userList[$keyUser]);
                }

                $data = new Cuti();

                $desireUser = $manager->getRepository(UserPersonal::class)->find($user);
                $absDate = $desireDate->format('d') . '-' .
                    $desireDate->format('m') . '-' .
                    $desireDate->format('Y');

                $data->setUserId($desireUser);
                $data->setTanggal($desireDate->format('d'));
                $data->setBulan($desireDate->format('m'));
                $data->setTahun($desireDate->format('Y'));
                $data->setAbsDate(new \DateTime($absDate));
                $data->setIsvalidated(1);
                $data->setValidatedBy($loginUser);
                $data->setDescription('Cuti Bersama : ' . $request->get('description'));
                $data->setHashDate($desireUser, $absDate);
                $data->setDayGroup($grId);

                array_push($newCuti, $data);
            }

            foreach ($newCuti as $key => $item) {
                foreach ($dataCutiOld as $keyCuti => $itemCuti) {
                    if ($itemCuti->getHashDate() == $item->getHashDate()) {
                        unset($newCuti[$key]);
                    }
                }
            }

            foreach ($newCuti as $newInsert) {
                $manager->persist($newInsert);
            }

            /**
             * Input Holiday.
             */
            $dataHoliday = new Holiday();

            $dataHoliday->setInputBy($loginUser);
            $dataHoliday->setDays($desireDate->format('d'));
            $dataHoliday->setMonth($desireDate->format('m'));
            $dataHoliday->setYear($desireDate->format('2017'));
            $dataHoliday->setTitle('Cuti Bersama : ' . $request->get('description'));
            $dataHoliday->setCreatedAt(new \DateTime());
            $dataHoliday->setDay($desireDate);

            $manager->persist($dataHoliday);

            try {
                $manager->flush();

                $this->get('session')->getFlashBag()->add('message_success', 'Proses telah berhasil di tambahkan.');
            } catch (UniqueConstraintViolationException $e) {
                $this->get('session')->getFlashBag()->add('message_failure', 'Error terkait : ' . $e);
            }

            return $this->redirectToRoute('office_user_success', [
                'proc_title' => 'Permintaan Cuti',
            ]);
        }

        return $this->render('OfficeBundle:dayoff:input-special.html.twig', ['data' => $data,]);
    }

    public function listSpecialAction()
    {
        $manager = $this->getDoctrine()->getManager();

        $data = $manager->getRepository(UserPersonal::class)->findAll();

        return $this->render('OfficeBundle:dayoff:view-quotas.html.twig', ['data' => $data]);
    }

    public function listDayTypeAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $data = $em->getRepository(DayType::class)->findBy(['isDeleted' => 0]);

        return $this->render('OfficeBundle:dayoff:view-day-type.html.twig', ['data'=>$data]);
    }

    public function editDayTypeAction(Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('office_admin_dayoff_type_list');
        }

        $manager = $this->getDoctrine()->getManager();

        $data = $manager->getRepository(DayType::class)->find($request->get('type_id'));

        if ($request->getMethod() === 'POST') {
            $dayName = ucwords($request->get('day-type'));

            $data->setName($dayName);
            $data->setCount($request->get('day-long'));

            $manager->persist($data);
            $manager->flush();

            return $this->redirectToRoute('office_admin_dayoff_type_list');
        }

        return $this->render('OfficeBundle:admin:input-day-type.html.twig', ['data' => $data, 'btn_name' => 'Update Data']);
    }

    public function deleteDayTypeAction(Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('office_admin_dayoff_type_list');
        }

        $manager = $this->getDoctrine()->getManager();

        $data = $manager->getRepository(DayType::class)->find($request->get('type_id'));

        $data->setIsDeleted(1);

        $manager->persist($data);
        $manager->flush();

        return $this->redirectToRoute('office_admin_dayoff_type_list');
    }

    public function dummyAction()
    {
        $manager = $this->getDoctrine()->getManager();
        $userdata = $this->get('security.token_storage')->getToken()->getUser();

        $quotas = 100;

        $dateStart = \DateTime::createFromFormat('d-m-Y', '14-08-2017');
        $dateEnd = \DateTime::createFromFormat('d-m-Y', '15-08-2017');
        $interval = $dateStart->diff($dateEnd)->format('%a');

        $negativeInterval = new \DateInterval('P1D');
        $negativeInterval->invert = 1;
        $dateStart->add($negativeInterval);
        $cutiRaw = $manager->getRepository(Cuti::class)->findAll();

        //        $cutiData = new ArrayCollection($cutiRaw);
        $newAdd = [];


        for ($i = 0; $i <= $interval; ++$i) {
            $dateStart->add(new \DateInterval('P1D'));
            $day = $dateStart->format('d');
            $month = $dateStart->format('m');
            $year = $dateStart->format('Y');
            $abs = \DateTime::createFromFormat('d m Y', $day . ' ' . $month . ' ' . $year);
            $absDate = $day . '-' . $month . '-' . $year;

            $data = new Cuti();
            $data->setUserId($userdata);
            $data->setTanggal((int)$day);
            $data->setBulan((int)$month);
            $data->setTahun((int)$year);
            $data->setAbsDate($abs);
            $data->setIsValidated(0);
            $data->setHashDate($userdata, $absDate);
            $data->setDescription('Something');

            // $manager->persist($data);
            array_push($newAdd, $data);

            $quotas = $quotas - 1;

            if ($quotas == 0) {
                $this->get('session')->getFlashBag()->add('message_failure', 'Batas pengajuan anda melebihi kuota yang anda miliki.');

                return $this->redirectToRoute('office_user_success', [
                'proc_title' => 'Permintaan Cuti',
                ]);
            }
        }

        // DO THE HARLEMSHAKE

        foreach ($newAdd as $key => $item) {
            foreach ($cutiRaw as $keyCuti => $itemCuti) {
                if ($itemCuti->getHashDate() == $item->getHashDate()) {
                    unset($newAdd[$key]);
                }
            }
        }
    }
}
