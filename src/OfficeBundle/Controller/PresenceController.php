<?php

namespace OfficeBundle\Controller;

use Doctrine\Common\Util\Debug;
use OfficeBundle\Entity\Device;
use OfficeBundle\Entity\Dummy;
use OfficeBundle\Entity\Holiday;
use OfficeBundle\Entity\Shift;
use OfficeBundle\Entity\UserPersonal;
use OfficeBundle\Entity\UserPresence;
use OfficeBundle\Entity\CompanyProfile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\User;
use DoctrineExtensions\Query\Mysql;

class PresenceController extends Controller
{
    public function indexAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        // initiate month and date
        $givenMonth = date('m');
        $givenYear = date('Y');
        $givenCompany = null;

        $yearPop = [date('Y')];
        for ($i = 1; $i < 5; ++$i) {
            array_push($yearPop, date('Y', strtotime('-' . $i . ' year')));
        }

        if ($request->get('month') != null) {
            $givenMonth = $request->get('month');
        }

        if ($request->get('year') != null) {
            $givenYear = $request->get('year');
        }

        if ($request->get('company') != null) {
            $givenCompany = $request->get('company');
        }

        $userData = $manager->getRepository(UserPersonal::class)->findAll();

        if ($request->get('company') != null) {
            if ($request->get('company') != 0) {
                $userData = $manager->getRepository(UserPersonal::class)->findBy([
                    'penempatan' => $request->get('company'),
                ]);
            }
        }

        $presenceDataRaw = $manager->getRepository(UserPresence::class)->findBy([
            'month' => $givenMonth,
            'year' => $givenYear,
        ]);

        /*
         * Calculating absence and presence of user.
         */

        $dataAbsen = [];

        $allDataPresence = $manager->getRepository(UserPresence::class)->findBy([
            'month' => $givenMonth,
            'year' => $givenYear,
        ]);

        $arrDuplicate = [];

        foreach ($allDataPresence as $item) {
            $item->setAbsoluteDay(base64_encode($item->getUserId()->getId() .' '. $item->getCreatedAt()->format('d m Y')));
            array_push($arrDuplicate, $item->getAbsoluteDay());
        }


        $unique = array_unique($arrDuplicate, SORT_REGULAR);

        $diffCellUniq = array_diff_key($arrDuplicate, $unique);

        foreach ($diffCellUniq as $uniqueKey => $uniqueValue) {
            unset($allDataPresence[$uniqueKey]);
        }

        foreach ($allDataPresence as $item) {
            foreach ($userData as $user) {
                if ($item->getUserId() == $user) {
                    $user->setPresenceRaw($user->getPresenceRaw() + 1);
                }
            }
        }

        $company = $manager->getRepository(CompanyProfile::class)->findAll();

        $dayCount = cal_days_in_month(CAL_GREGORIAN, $givenMonth, $givenYear);

        $monthHoliday = 0;

        for ($i = 1; $i <= $dayCount; $i++) {
            $dayName = new \DateTime($i . '-' . $givenMonth . '-' . $givenYear);

            if ($dayName->format('D') == 'Sun') {
                $monthHoliday++;
            }
        }

        $holiday = $manager->getRepository(Holiday::class)->findBy(['month' => $givenMonth, 'year' => $givenYear]);

        $monthHoliday = $monthHoliday + count($holiday);

        return $this->render('OfficeBundle:presence:index.html.twig', [
            'data' => $userData,
            'yearPop' => $yearPop,
            'presence' => $presenceDataRaw,
            'month' => $givenMonth,
            'year' => $givenYear,
            'dayCount' => $dayCount - $monthHoliday,
            'company' => $company,
        ]);
    }

    public function presenceDetailAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $managerConfig = $manager->getConfiguration();
        $dateNow = new \DateTime();
        $givenMonth = $request->get('month');
        $givenYear = $request->get('year');
        $requestedDate = new \DateTime('1-'.$givenMonth.'-'.$givenYear);
        $presenceRepository = $manager->getRepository(UserPresence::class);
        $dayOfMonth = cal_days_in_month(CAL_GREGORIAN, $requestedDate->format('m'), $requestedDate->format('Y'));
        
        $user = $manager->getRepository(UserPersonal::class)->find($request->get('id'));

        $dataMasuk = [];
        $dataPulang = [];

        $dataMasukRaw = $presenceRepository->findBy([
            'state' => -1,
            'userId' => $request->get('id'),
            'month' => $givenMonth,
            'year' => $givenYear,
        ]);

        $dataPulangRaw = $presenceRepository->findBy([
            'state' => 1,
            'userId' => $request->get('id'),
            'month' => $givenMonth,
            'year' => $givenYear,
        ]);

        foreach ($dataMasukRaw as $item) {
            $dataMasuk[$item->getDay()]['state'] = -1;
            $dataMasuk[$item->getDay()]['date'] = $item->getCreatedAt();
        }

        foreach ($dataPulangRaw as $item) {
            $dataPulang[$item->getDay()]['state'] = 1;
            $dataPulang[$item->getDay()]['date'] = $item->getCreatedAt();
        }

        $holidayRaw = $manager->getRepository(Holiday::class)->findBy([
            'month' => $givenMonth,
            'year' => $givenYear,
        ]);
        $holiday = [];

        foreach ($holidayRaw as $item) {
            $holiday[$item->getDays()]['state'] = 1;
            $holiday[$item->getDays()]['title'] = $item->getTitle();
        }

        $variable['month'] = \DateTime::createFromFormat('m', $givenMonth)->format('M');
        $variable['year'] = \DateTime::createFromFormat('Y', $givenYear)->format('Y');

        $managerConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');

        $variable['cuti'] = $manager->createQueryBuilder()
            ->select('c')
            ->from('OfficeBundle:Cuti', 'c')
            ->where('c.userId = :userId')
            ->andWhere('c.isValidated = 1')
            ->andWhere('MONTH(c.absDate) = :desireMonth')
            ->setParameter('userId', $request->get('id'))
            ->setParameter('desireMonth', $givenMonth)
            ->getQuery()->getResult();

        $variable['attachment'] = $manager->createQueryBuilder()
            ->select('a')
            ->from('OfficeBundle:Attachment', 'a')
            ->where('a.userId = :userId')
            ->andWhere('MONTH(a.tglMulai) = :desireMonth')
            ->setParameter('userId', $request->get('id'))
            ->setParameter('desireMonth', $givenMonth)
            ->getQuery()->getResult();

        /**
         * Getting Detail of attachment.
         */
        $newDatePopulate = [];

        foreach ($variable['attachment'] as $itemRaw) {
            $startDate = $itemRaw->getTglMulai();
            $endDate = $itemRaw->getTglAkhir();

            $interval = \DateInterval::createFromDateString('1 day');
            $period = new \DatePeriod($startDate, $interval, $endDate);

            foreach ($period as $dt) {
                $newDatePopulate[$dt->format('d-m-Y')] = [$itemRaw->getAbsen(), $itemRaw->getDescription()];
            }

            $newDatePopulate[$endDate->format('d-m-Y')] = [$itemRaw->getAbsen(), $itemRaw->getDescription()];
        }

        /**
         * Checking if populated date out of month.
         */
        foreach ($newDatePopulate as $key => $value) {
            $checkDate = new \DateTime($key);

            if ($checkDate->format('m') != $givenMonth) {
                unset($newDatePopulate[$key]);
            }
        }

        return $this->render('OfficeBundle:presence:detail.html.twig', [
            'monthCount' => $dayOfMonth,
            'dataMasuk' => $dataMasuk,
            'dataPulang' => $dataPulang,
            'holiday' => $holiday,
            'user' => $user,
            'variable' => $variable,
            'newDatePopulate' => $newDatePopulate,
        ]);
    }

    public function getActivationCodeAction(Request $request)
    {
        $vc = $request->query->get('vc');

        $data = $this->getDoctrine()->getManager()
            ->createQuery('SELECT e FROM OfficeBundle:Device e where e.vc = :vc')
            ->setParameter('vc', $vc)->getArrayResult()[0];

        echo $data['ac'] . $data['sn'];
    }

    public function presenceAction(Request $request)
    {
        $userdata = null;
        $presenceData = null;

        if ($request->get('userlog') != null) {
            $userdata = $this->getDoctrine()->getManager()->getRepository(UserPersonal::class)->findOneBy(['username' => $request->get('userlog')]);

            $timestamp = $request->get('timestamp');
            $presenceData = $this->getDoctrine()->getManager()->createQueryBuilder()
                ->select('u')
                ->from('OfficeBundle:UserPresence', 'u')
                ->where('u.userId = :userId')
                ->setParameter('userId', $userdata->getId())
                ->andWhere('u.day = :day')
                ->andWhere('u.month = :month')
                ->andWhere('u.year = :year')
                ->setParameter('day', date('d', $timestamp))
                ->setParameter('month', date('m', $timestamp))
                ->setParameter('year', date('Y', $timestamp))
                ->setMaxResults(1)->orderBy('u.id', 'DESC')->getQuery()->getResult()[0];

        }

        return $this->render('OfficeBundle:presence:core.html.twig', ['userdata' => $userdata, 'presenceData' => $presenceData]);
    }

    public function presenceProcessAction(Request $request, $username)
    {
        $data = $this->getDoctrine()->getManager()->getRepository(UserPersonal::class)->findOneBy(['username' => $username]);

        $finger = $data->getFinger();
        $id = $data->getId();
        $verifyUrl = 'http://' . $request->headers->get('host') . $this->generateUrl('office_presence_do');
        $getAcUrl = 'http://' . $request->headers->get('host') . $this->generateUrl('office_presence_user_get_ac');

        if ($data != null) {
            echo "$id;" . $finger->getFingerData() . ';SecurityKey;10;' . $verifyUrl . ';' . $getAcUrl . ';extraParams';
        }
    }

    public function doPresenceAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        if (isset($_POST['VerPas']) && !empty($_POST['VerPas'])) {
            $dataFinger = explode(';', $_POST['VerPas']);
            $user_id = $dataFinger[0];
            $vStamp = $dataFinger[1];
            $time = $dataFinger[2];
            $sn = $dataFinger[3];

            /**
             * Getting data fingerprint for specified user.
             */
            $fingerData = $manager->getRepository(UserPersonal::class)->find($user_id)->getFinger();

            /**
             * Getting device information according Serial number.
             */
            $device = $manager->getRepository(Device::class)->findOneBy(['sn' => $sn]);

            $user_name = $manager->getRepository(UserPersonal::class)->find($user_id)->getNama();

            $salt = md5($sn . $fingerData->getFingerData() . $device->getVc() . $time . $user_id . $device->getVkey());

            $data = [];
            $dateNow = new \DateTime();

            $user = $manager->getRepository(UserPersonal::class)->find($user_id);

            $shift = $user->getJob()->getShift();
            $data['user_id'] = $user->getId();
            $startHour = $shift->getStartTime();

            // Init new UserPresence
            $presenceData = UserPresence::createDefault($user, $dateNow, $shift);
            $startHour = new \DateTime($shift->getStartTime()->format('H:i'));

            /*
             * Confusing algorithm start here
             */
            if ($startHour->format('a') == 'am') { // For Morning shift
                /**
                 * ALGORITHM FOR MORNING SHIFT.
                 */
                $tmpPresence = $manager->getRepository(UserPresence::class)->createQueryBuilder('up')
                    ->where('up.userId = :userId')
                    ->andWhere('up.createdAt LIKE :givenDate')
                    ->setParameter('userId', $user->getId())
                    ->setParameter('givenDate', '%' . $dateNow->format('Y-m-d') . '%')->getQuery()->getResult();

                $endTime = new \DateTime($shift->getEndTime()->format('H:i'));

                if (count($tmpPresence) == 0 && $dateNow < $startHour->add(new \DateInterval('PT1H'))) {

                    /**
                     * Normal for job start.
                     */
                    $presenceData->setState(-1);
                    $this->get('session')->getFlashBag()->add(
                        'presence_message',
                        'Terima kasih <strong>' . $user->getNama() .
                        '</strong>, absensi <strong>MASUK</strong> kamu telah diterima pada ' . $dateNow->format('h:i A')
                    );
                } elseif (count($tmpPresence) == 0 && $dateNow > $startHour->add(new \DateInterval('PT6H'))) {

                    /**
                     * If user forget to input when job start
                     * while execute job end.
                     */
                    $presenceData->setState(1);
                    $presenceData->setDescription('LUPA CHECKLOG MASUK');

                    $this->get('session')->getFlashBag()->add(
                        'presence_message',
                        'Terima kasih <strong>' . $user->getNama() .
                        '</strong>, absensi <strong>PULANG</strong> kamu telah diterima pada ' . $dateNow->format('h:i A')
                    );
                } elseif (count($tmpPresence) == 1 && $dateNow > $endTime) {

                    /**
                     * Normal presence for job done.
                     */
                    $presenceData->setState(1);
                    $this->get('session')->getFlashBag()->add(
                        'presence_message',
                        'Terima kasih <strong>' . $user->getNama() .
                        '</strong>, absensi <strong>PULANG</strong> kamu telah diterima pada ' . $dateNow->format('h:i A')
                    );
                } elseif (count($tmpPresence) == 1 && $dateNow < $endTime) {
                    if ($dateNow < $startHour->add(new \DateInterval('PT3H'))) {

                        /**
                         * This statement to avoid multi-checklog when job start.
                         */
                        $this->get('session')->getFlashBag()->add(
                            'presence_failure',
                            'Maaf, <strong>' . $user->getNama() .
                            '</strong>. Absensi masuk kamu sudah kami terima sebelumnya.'
                        );

                        return $this->redirectToRoute('office_presence_interface');
                    }

                    /**
                     * When user decide to done the job earlier
                     * the Requirements is job must be start at least 3 hour after job start's time.
                     */
                    $presenceData->setState(1);
                    $presenceData->setDescription('PULANG LEBIH AWAL');
                    $this->get('session')->getFlashBag()->add(
                        'presence_message',
                        'Terima kasih <strong>' . $user->getNama() .
                        '</strong>, absensi <strong>PULANG</strong> kamu telah diterima pada ' . $dateNow->format('h:i A') . ', namun dengan status <strong>PULANG LEBIH AWAL</strong>'
                    );
                } else {
                    $this->get('session')->getFlashBag()->add(
                        'presence_failure',
                        'Maaf, <strong>' . $user->getNama() .
                        '</strong>. Absensi pulang kamu sudah kami terima sebelumnya.'
                    );
                    return $this->redirectToRoute('office_presence_interface');
                }
            } elseif ($startHour->format('a') == 'pm') {
                /**
                 * ALGORITHM FOR NIGHT SHIFT.
                 */
                $startTime = \DateTime::createFromFormat('H:i a', $shift->getStartTime()->format('H:i a'));
                $endTime = \DateTime::createFromFormat('H:i a', $shift->getEndTime()->format('H:i a'));

                $duration = ($endTime->getTimestamp() - $startTime->getTimestamp()) / 3600;
                $tmpPresence = $manager->getRepository(UserPresence::class)->createQueryBuilder('up')
                    ->where('up.userId = :id')
                    ->andWhere('up.createdAt LIKE :givenDate')
                    ->setParameter('id', $user->getId())
                    ->setParameter('givenDate', '%' . $dateNow->format('Y-m-d') . '%')->getQuery()->getResult();
                $toleranceStart = $startTime->sub(new \DateInterval('PT1H'));
                $toleranceEnd = $startTime->add(new \DateInterval('PT15M'));
                if (count($tmpPresence) == 0) { // NORMAL FOR JOB START
                    if ($dateNow > $toleranceStart && $dateNow < $toleranceEnd) {
                        $presenceData->setState(-1);
                    } elseif ($dateNow >= \DateTime::createFromFormat('H:i a', $shift->getEndTime()->format('H:i a'))) {
                        $presenceData->setState(1); // NORMAL FOR JOB DONE
                    }
                } elseif (count($tmpPresence) == 1) {
                    if ($tmpPresence->getState() == 1) {
                        $presenceData->setState(-1);
                    } else {
                        $presenceData->setState(-1);
                        $presenceData->setDescription('Malfungsi absen malam');
                    }
                }
            }

            $manager->persist($presenceData);
            $manager->flush();

//            echo $this->redirectToRoute('office_presence_interface');
            echo 'http://' . $request->headers->get('host') . $this->generateUrl('office_presence_interface');
        }
    }

    public function dopresbackupAction(Request $request, $id)
    {
        $data = [];
        $dateNow = new \DateTime();

        $manager = $this->getDoctrine()->getManager();
        $user = $manager->getRepository(UserPersonal::class)->find($id);
        $shift = $user->getJob()->getShift();
        $data['user_id'] = $user->getId();
        $startHour = $shift->getStartTime();

        // Init new UserPresence
        $presenceData = UserPresence::createDefault($user, $dateNow, $shift);
        $startHour = new \DateTime($shift->getStartTime()->format('H:i'));

        /*
         * Confusing algorithm start here
         */
        if ($startHour->format('a') == 'am') { // For Morning shift
            /**
             * ALGORITHM FOR MORNING SHIFT.
             */
            $tmpPresence = $manager->getRepository(UserPresence::class)->createQueryBuilder('up')
                ->where('up.userId = :userId')
                ->andWhere('up.createdAt LIKE :givenDate')
                ->setParameter('userId', $user->getId())
                ->setParameter('givenDate', '%' . $dateNow->format('Y-m-d') . '%')->getQuery()->getResult();

            if (count($tmpPresence) == 0 && $dateNow < $startHour->add(new \DateInterval('PT1H'))) {
                // normal presence for job start
                $presenceData->setState(-1);
            } elseif (count($tmpPresence) == 0 && $dateNow > $startHour->add(new \DateInterval('PT6H'))) {
                // if user forget to input when job start
                $presenceData->setState(1);
            } elseif (count($tmpPresence) == 1) { // normal presence for job done
                $presenceData->setState(1);
            } else {
                return 'Unknown error detected';
            }
        } elseif ($startHour->format('a') == 'pm') {
            /**
             * ALGORITHM FOR NIGHT SHIFT.
             */
            $startTime = \DateTime::createFromFormat('H:i a', $shift->getStartTime()->format('H:i a'));
            $endTime = \DateTime::createFromFormat('H:i a', $shift->getEndTime()->format('H:i a'));

            $duration = ($endTime->getTimestamp() - $startTime->getTimestamp()) / 3600;
            $tmpPresence = $manager->getRepository(UserPresence::class)->createQueryBuilder('up')
                ->where('up.userId = :id')
                ->andWhere('up.createdAt LIKE :givenDate')
                ->setParameter('id', $user->getId())
                ->setParameter('givenDate', '%' . $dateNow->format('Y-m-d') . '%')->getQuery()->getResult();
            $toleranceStart = $startTime->sub(new \DateInterval('PT1H'));
            $toleranceEnd = $startTime->add(new \DateInterval('PT15M'));
            if (count($tmpPresence) == 0) { // NORMAL FOR JOB START
                if ($dateNow > $toleranceStart && $dateNow < $toleranceEnd) {
                    $presenceData->setState(-1);
                } elseif ($dateNow >= \DateTime::createFromFormat('H:i a', $shift->getEndTime()->format('H:i a'))) {
                    $presenceData->setState(1); // NORMAL FOR JOB DONE
                }
            } elseif (count($tmpPresence) == 1) {
                if ($tmpPresence->getState() == 1) {
                    $presenceData->setState(-1);
                } else {
                    $presenceData->setState(0);
                }
            }
        }

        $manager->persist($presenceData);
        $manager->flush();

        return var_dump($data);
    }

    public function dummyDataAction()
    {
        $manager = $this->getDoctrine()->getManager();

        $data = new Dummy();
        $data->setDescription('Something');

        $manager->persist($data);
        $manager->flush();

        return 'ok';
    }

    public function legacyChecklogAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        if ($request->getMethod() === 'POST') {
            $id = $request->get('user_id');
            $data = [];
            $dateNow = new \DateTime();

            $manager = $this->getDoctrine()->getManager();
            $user = $manager->getRepository(UserPersonal::class)->find($id);

            if (!$user instanceof UserPersonal) {
                return 'User not found';
            }

            $shift = $user->getJob()->getShift();

            if (!$shift instanceof Shift) {
                return 'Shift not fill up yet';
            }

            $data['user_id'] = $user->getId();
            $startHour = $shift->getStartTime();

            // Init new UserPresence
            $presenceData = UserPresence::createDefault($user, $dateNow, $shift);
            $startHour = new \DateTime($shift->getStartTime()->format('H:i'));

            /**
             * Confusing algorithm start here
             * don't dare to change.
             */
            if ($startHour->format('a') == 'am') { // For Morning shift

                /**
                 * ALGORITHM FOR MORNING SHIFT.
                 */
                $tmpPresence = $manager->getRepository(UserPresence::class)->createQueryBuilder('up')
                    ->where('up.userId = :userId')
                    ->andWhere('up.createdAt LIKE :givenDate')
                    ->setParameter('userId', $user->getId())
                    ->setParameter('givenDate', '%' . $dateNow->format('Y-m-d') . '%')->getQuery()->getResult();

                $endTime = new \DateTime($shift->getEndTime()->format('H:i'));

                if (count($tmpPresence) == 0 && $dateNow < $startHour->add(new \DateInterval('PT1H'))) {

                    /**
                     * Normal for job start.
                     */
                    $presenceData->setState(-1);
                    $this->get('session')->getFlashBag()->add(
                        'presence_message',
                        'Terima kasih <strong>' . $user->getNama() .
                        '</strong>, absensi <strong>MASUK</strong> kamu telah diterima pada ' . $dateNow->format('h:i A')
                    );
                } elseif (count($tmpPresence) == 0 && $dateNow > $startHour->add(new \DateInterval('PT6H'))) {

                    /**
                     * If user forget to input when job start
                     * while execute job end.
                     */
                    $presenceData->setState(1);
                    $presenceData->setDescription('LUPA CHECKLOG MASUK');

                    $this->get('session')->getFlashBag()->add(
                        'presence_message',
                        'Terima kasih <strong>' . $user->getNama() .
                        '</strong>, absensi <strong>PULANG</strong> kamu telah diterima pada ' . $dateNow->format('h:i A')
                    );
                } elseif (count($tmpPresence) == 1 && $dateNow > $endTime) {

                    /**
                     * Normal presence for job done.
                     */
                    $presenceData->setState(1);
                    $this->get('session')->getFlashBag()->add(
                        'presence_message',
                        'Terima kasih <strong>' . $user->getNama() .
                        '</strong>, absensi <strong>PULANG</strong> kamu telah diterima pada ' . $dateNow->format('h:i A')
                    );
                } elseif (count($tmpPresence) == 1 && $dateNow < $endTime) {
                    if ($dateNow < $startHour->add(new \DateInterval('PT3H'))) {

                        /**
                         * This statement to avoid multi-checklog when job start.
                         */
                        $this->get('session')->getFlashBag()->add(
                            'presence_failure',
                            'Maaf, <strong>' . $user->getNama() .
                            '</strong>. Absensi masuk kamu sudah kami terima sebelumnya.'
                        );

                        return $this->redirectToRoute('office_presence_legacy');
                    }

                    /**
                     * When user decide to done the job earlier
                     * the Requirements is job must be start at least 3 hour after job start's time.
                     */
                    $presenceData->setState(1);
                    $presenceData->setDescription('PULANG LEBIH AWAL');
                    $this->get('session')->getFlashBag()->add(
                        'presence_message',
                        'Terima kasih <strong>' . $user->getNama() .
                        '</strong>, absensi <strong>PULANG</strong> kamu telah diterima pada ' . $dateNow->format('h:i A') . ', namun dengan status <strong>PULANG LEBIH AWAL</strong>'
                    );
                } else {
                    $this->get('session')->getFlashBag()->add(
                        'presence_failure',
                        'Maaf, <strong>' . $user->getNama() .
                        '</strong>. Absensi pulang kamu sudah kami terima sebelumnya.'
                    );
                    return $this->redirectToRoute('office_presence_legacy');
                }

                /**
                 * Second algorithm for night shift.
                 */
            } elseif ($startHour->format('a') == 'pm') {
                $startTime = \DateTime::createFromFormat('H:i a', $shift->getStartTime()->format('H:i a'));
                $endTime = \DateTime::createFromFormat('H:i a', $shift->getEndTime()->format('H:i a'));

                $duration = ($endTime->getTimestamp() - $startTime->getTimestamp()) / 3600;
                $tmpPresence = $manager->getRepository(UserPresence::class)->createQueryBuilder('up')
                    ->where('up.userId = :id')
                    ->andWhere('up.createdAt LIKE :givenDate')
                    ->setParameter('id', $user->getId())
                    ->setParameter('givenDate', '%' . $dateNow->format('Y-m-d') . '%')->getQuery()->getResult();
                $toleranceStart = $startTime->sub(new \DateInterval('PT1H'));
                $toleranceEnd = $startTime->add(new \DateInterval('PT15M'));
                if (count($tmpPresence) == 0) { // NORMAL FOR JOB START
                    if ($dateNow > $toleranceStart && $dateNow < $toleranceEnd) {
                        $presenceData->setState(-1);
                    } elseif ($dateNow >= \DateTime::createFromFormat('H:i a', $shift->getEndTime()->format('H:i a'))) {
                        $presenceData->setState(1); // NORMAL FOR JOB DONE
                    }
                } elseif (count($tmpPresence) == 1) {
                    if ($tmpPresence->getState() == 1) {
                        $presenceData->setState(-1);
                    } else {
                        $presenceData->setState(0);
                    }
                }
            }

            $manager->persist($presenceData);
            $manager->flush();
        }

        $data = $manager->createQuery(
            'SELECT job from OfficeBundle:UserJob job where job.shift is not NULL'
        );

        return $this->render('OfficeBundle:presence:legacy.html.twig', [
            'data' => $data->getResult(),
        ]);
    }
}
