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
use Symfony\Component\Yaml\Yaml;

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
            array_push($yearPop, date('Y', strtotime('-'.$i.' year')));
        }

        if (null != $request->get('month')) {
            $givenMonth = $request->get('month');
        }

        if (null != $request->get('year')) {
            $givenYear = $request->get('year');
        }

        if (null != $request->get('company')) {
            $givenCompany = $request->get('company');
        }

        $userData = $manager->getRepository(UserPersonal::class)->findAll();

        if (null != $request->get('company')) {
            if (0 != $request->get('company')) {
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
            $item->setAbsoluteDay(base64_encode($item->getUserId()->getId().' '.$item->getCreatedAt()->format('d m Y')));
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

        for ($i = 1; $i <= $dayCount; ++$i) {
            $dayName = new \DateTime($i.'-'.$givenMonth.'-'.$givenYear);

            if ('Sun' == $dayName->format('D')) {
                ++$monthHoliday;
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
            ->andWhere(':desireMonth BETWEEN MONTH(a.tglMulai) AND MONTH(a.tglAkhir)')
            ->andWhere('a.isValidated = 1')
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

        /*
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
            'givenYear' => $givenYear,
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

        echo $data['ac'].$data['sn'];
    }

    public function presenceAction(Request $request)
    {
        $userdata = null;
        $presenceData = null;

        try {
            $notif = Yaml::parse(file_get_contents(dirname(__DIR__).'/notification.yml'));
        } catch (\Exception $e) {
            $notif = null;
        }

        if (null != $request->get('userlog')) {
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
                ->setMaxResults(1)->orderBy('u.id', 'DESC')->getQuery()->getResult();
        }

        return $this->render('OfficeBundle:presence:core.html.twig', [
            'userdata' => $userdata,
            'presenceData' => $presenceData,
            'notif' => $notif,
        ]);
    }

    public function presenceProcessAction(Request $request, $username)
    {
        $data = $this->getDoctrine()->getManager()->getRepository(UserPersonal::class)->findOneBy(['username' => $username]);

        $finger = $data->getFinger();
        $id = $data->getId();
        $verifyUrl = 'http://'.$request->headers->get('host').$this->generateUrl('office_presence_do');
        $getAcUrl = 'http://'.$request->headers->get('host').$this->generateUrl('office_presence_user_get_ac');

        if (null != $data) {
            echo "$id;".$finger->getFingerData().';SecurityKey;10;'.$verifyUrl.';'.$getAcUrl.';extraParams';
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

            $salt = md5($sn.$fingerData->getFingerData().$device->getVc().$time.$user_id.$device->getVkey());

            $data = [];
            $dateNow = new \DateTime();

            $user = $manager->getRepository(UserPersonal::class)->find($user_id);

            $shift = $user->getJob()->getShift();
            $data['user_id'] = $user->getId();
            $startHour = $shift->getStartTime();

            // Init new UserPresence
            $presenceData = UserPresence::createDefault($user, $dateNow, $shift);
            $startHour = new \DateTime($shift->getStartTime()->format('H:i'));
            
            // Init new UserPresence
            $presenceData = UserPresence::createDefault($user, $dateNow, $shift);
            $startHour = new \DateTimeImmutable($shift->getStartTime()->format('H:i'));
            $startStmp = strtotime($shift->getStartTime()->format('H:i'));
            $endHour = new \DateTimeImmutable($shift->getEndTime()->format('H:i'));
            $endStmp = strtotime($shift->getEndTime()->format('H:i'));

            if ($endStmp < $startStmp) {
                $endStmp = $endStmp + (24 * 3600);
            }
            
            $flash = [];

            $interval = round(abs($endStmp - $startStmp) / 3600, 2);

            $nonDecimalInterval = floor($interval);
            $decimalInterval = $interval - $nonDecimalInterval;
            $intervalVal = "PT0H";

            if ($decimalInterval > 0) {
                $intervalVal = "PT" . (int) $nonDecimalInterval . "H" . (int) $endTime->format("i") ."M";
            } else {
                $intervalVal = "PT".$nonDecimalInterval."H";
            }

            if (1 == $shift->getOffice()) { // Shift Kantor
                $tmpPresence = $manager->getRepository(
                    UserPresence::class
                )->createQueryBuilder('up')
                    ->where('up.userId = :userId')
                    ->andWhere('up.createdAt LIKE :givenDate')
                    ->setParameter('userId', $user->getId())
                    ->setParameter(
                        'givenDate',
                        '%'.$dateNow->format('Y-m-d').'%'
                    )->getQuery()->getResult();

                $endTime = new \DateTimeImmutable($shift->getEndTime()->format('H:i'));

                if (0 == count($tmpPresence)) {
                    $presenceData->setState(-1);
                    
                    $flash = array(
                        'presence_success' => 'Absensi anda pada '.$dateNow->format('H:i').
                        ' kami terima.',
                    );

                    if ($dateNow > $endTime) {
                        $presenceData->setState(1);
                        $flash = array(
                            'presence_info' => 'Absensi anda pada '.$dateNow->format('H:i').
                            ' kami terima, namun anda lupa checklog masuk',
                        );
                    }
                } elseif (1 == count($tmpPresence)) {
                    $presenceData->setState(1);
                    $flash = array(
                        'presence_success' => 'Absensi anda pada '.$dateNow->format('H:i').
                        ' kami terima.',
                    );

                    if ($dateNow < $endTime && $dateNow > $startHour->add(new \DateInterval($intervalVal))) {
                        $flash = array(
                            'presence_info' => 'Memutuskan untuk pulang lebih awal',
                        );
                    } else {
                        $flash = array(
                            'presence_info' => 'Data anda telah kami terima sebelumnya',
                        );
                    }
                }
            } elseif (0 == $shift->getOffice()) {
                $tmpPresence = $manager->getRepository(
                    UserPresence::class
                )->createQueryBuilder('up')
                    ->where('up.userId = :userId')
                    ->andWhere('up.createdAt LIKE :givenDate')
                    ->setParameter('userId', $user->getId())
                    ->setParameter(
                        'givenDate',
                        '%'.$dateNow->format('Y-m-d').'%'
                    )->getQuery()->getResult();

                $endTime = new \DateTimeImmutable($shift->getEndTime()->format('H:i'));

                if ('am' == $startHour->format('a')) { // Absensi Shift 1
                    if (0 == count($tmpPresence)) {
                        /*
                         * Datang Normal
                         */
                        if ($dateNow < $startHour->add(new \DateInterval('PT15M'))) {
                            $presenceData->setState(-1);
                            $flash = array(
                                'presence_success' => 'Absensi anda pada '.$dateNow->format('H:i').
                                ' kami terima.',
                            );
                        } elseif ($dateNow > $startHour && $dateNow < $endTime) {
                            /*
                             * Masuk Terlambat
                             */
                            if ($dateNow < $endTime) {
                                $presenceData->setState(-1);
                                $presenceData->setDescription('Terlambat');
                                $flash = array(
                                    'presence_info' => 'Absensi anda pada '.$dateNow->format('H:i').
                                    ' kami terima, tetapi dengan status terlambat',
                                );
                            }
                        } else if ($dateNow > $startHour && $dateNow < $startHour->add(new \DateInterval($intervalVal))) {
                            return new Response('HUBUNGI ADMIN');
                        }
                    } else {
                        /*
                         * Pulang Normal
                         */
                        if ($dateNow > $endTime) {
                            $presenceData->setState(1);
                            $flash = array(
                                'presence_success' => 'Absensi anda pada '.$dateNow->format('H:i').
                                ' kami terima.',
                            );
                        }
                    }
                } elseif ('pm' == $startHour->format('a')) {
                    if ($endTime->format('G') < '12') {
                        /*
                         * Filtering for cross-day presence shift.
                         * Midnight start and daylight end.
                         */
                        $secondInterval =  round(1/2 * ($interval));

                        $tmpPresence = null;

                        /**
                         * Fill the tmpPresence
                         */
                        if ($dateNow->format('G') > 12) {
                            $tmpPresence = $manager->getRepository(
                                UserPresence::class
                            )->createQueryBuilder('up')
                                ->where('up.userId = :userId')
                                ->andWhere('up.createdAt LIKE :givenDate')
                                ->setParameter('userId', $user->getId())
                                ->setParameter(
                                    'givenDate',
                                    '%'.$dateNow->format('Y-m-d').'%'
                                )->getQuery()->getResult();
                        } else {
                            $negativeInterval = new \DateInterval('P1D');
                            $negativeInterval->invert = true;

                            $dateYesterday = $dateNow->add($negativeInterval);

                            $tmpPresence = $manager->getRepository(
                                UserPresence::class
                            )->createQueryBuilder('up')
                                ->where('up.userId = :userId')
                                ->andWhere('up.createdAt LIKE :givenDate')
                                ->setParameter('userId', $user->getId())
                                ->setParameter(
                                    'givenDate',
                                    '%'.$dateYesterday->format('Y-m-d').'%'
                                )->getQuery()->getResult();
                        }

                        if ($dateNow < $startHour) {
                            // Statement 7
                            $presenceData->setState(-1);
                            $flash = array(
                                'presence_success' => 'Absensi anda pada '.$dateNow->format('H:i').
                                    ' telah kami terima.'
                            );
                        } else {
                            // Statement 8
                            $presenceData->setState(-1);
                            $flash = array(
                                'presence_success' => 'Absensi anda pada '.$dateNow->format('H:i').
                                    ' telah kami terima.'
                            );

                            if ($dateNow > $startHour->add(new \DateInterval('PT15M'))) {
                                if ($dateNow < $startHour->add(new \DateInterval('PT'.$secondInterval.'H'))) {
                                    // Statement 9
                                    $presenceData->setDescription('TERLAMBAT');
                                    $flash = array(
                                        'presence_info' => 'Absensi anda pada '.$dateNow->format('H:i').
                                            ' telah kami terima. Namun dengan status '. $presenceData->getDescription()
                                    );
                                } else {
                                    // Lebih dari jam masuk, jam toleransi dan interval.
                                    if ($dateNow < $endTime) {
                                        $presenceData->setState(1);
                                        if (count($tmpPresence) == 0) {
                                            // Statement 10a
                                            $presenceData->setDescription('PULANG LEBIH AWAL, LUPA CHECKLOG');
                                            $flash = array(
                                                'presence_info' => 'Absensi anda pada '.$dateNow->format('H:i').
                                                    ' telah kami terima. Namun dengan status '. $presenceData->getDescription()
                                            );
                                        } else {
                                            // Statement 10b
                                            $presenceData->setDescription('PULANG LEBIH AWAL');
                                            $flash = array(
                                                'presence_info' => 'Absensi anda pada '.$dateNow->format('H:i').
                                                    ' telah kami terima. Namun dengan status '. $presenceData->getDescription()
                                            );
                                        }
                                    } else {
                                        $presenceData->setState(1);
                                        $flash = array(
                                            'presence_success' => 'Absensi anda pada '.$dateNow->format('H:i').
                                                ' telah kami terima.'
                                        );

                                        if (count($tmpPresence) == 0) {
                                            // Statement 10c
                                            $presenceData->setDescription('LUPA CHECKLOG MASUK');
                                            $flash = array(
                                                'presence_info' => 'Absensi anda pada '.$dateNow->format('H:i').
                                                    ' telah kami terima. Namun dengan status '. $presenceData->getDescription()
                                            );
                                        }
                                    }
                                }
                            }
                        }

                    } else {
                        /*
                         * This is for normal afternoon presence.
                         */
                        $tmpPresence = $manager->getRepository(
                            UserPresence::class
                        )->createQueryBuilder('up')
                            ->where('up.userId = :userId')
                            ->andWhere('up.createdAt LIKE :givenDate')
                            ->setParameter('userId', $user->getId())
                            ->setParameter(
                                'givenDate',
                                '%'.$dateNow->format('Y-m-d').'%'
                            )->getQuery()->getResult();

                        if (0 == count($tmpPresence)) {
                            if ($dateNow <= $startHour) {
                                // Statement 1
                                $presenceData->setState(-1);
                                $flash = array(
                                    'presence_success' => 'Absensi anda pada '.$dateNow->format('H:i').
                                        ' kami terima',
                                );
                            }

                            if ($dateNow > $startHour && $dateNow < $startHour->add(new \DateInterval('PT15M'))) {
                                // Statement 2
                                $presenceData->setState(-1);
                                $flash = array(
                                    'presence_success' => 'Absensi anda pada '.$dateNow->format('H:i').
                                        ' kami terima',
                                );
                            }

                            if ($dateNow > $startHour->add(new \DateInterval('PT15M'))) {
                                if ($dateNow < $startHour->add(new \DateInterval($intervalVal))) {
                                    // Statement 3
                                    $presenceData->setState(-1);
                                    $presenceData->setDescription('MASUK TERLAMBAT');
                                    $flash = array(
                                        'presence_info' => 'Absensi anda pada '.$dateNow->format('H:i').
                                            ' kami terima, tetapi dengan status terlambat',
                                    );
                                }

                                // Statement 4
                                $presenceData->setState(-1);
                                $flash = array(
                                    'presence_info' => 'Absensi anda pada '.$dateNow->format('H:i').
                                        ' kami terima, namun anda lupa checklog masuk',
                                );

                                if ($dateNow < $startHour->add(new \DateInterval('PT'.$interval * (3 / 4).'H'))) {
                                    // Statement 4a
                                    $presenceData->setState(1);
                                    $presenceData->setDescription('PULANG LEBIH AWAL');
                                    $flash = array(
                                        'presence_info' => 'Absensi anda pada '.$dateNow->format('H:i').
                                            ' kami terima, pulang lebih awal dan lupa checklog masuk',
                                    );
                                }
                            }
                        } else {
                            if ($dateNow < $endTime) {
                                // Statement 5
                                $presenceData->setState(1);
                                $flash = array(
                                    'presence_info' => 'Memutuskan pulang lebih awal ?. Absensi anda pada '.$dateNow->format('H:i').
                                        ' kami terima.',
                                );
                            }

                            if ($dateNow >= $endTime) {
                                // Statement 6
                                $presenceData->setState(1);
                                $flash = array(
                                    'presence_success' => 'Absensi anda pada '.$dateNow->format('H:i').
                                        ' kami terima.',
                                );
                            }
                        }
                    }
                }
            } else {
                return new Response('Ohno', 406);
            }

            try {
                $manager->persist($presenceData);
                $manager->flush();

            } catch (\Exception $e) {
                $flash['presence_failure'] = 'Data anda tidak terekam, silahkan hubungi Admin';
            }

            foreach ($flash as $key => $value) {
                $ymlarray = [$key => $value];

                $ymlDump = Yaml::dump($ymlarray);

                $open = file_put_contents(dirname(__DIR__).'/notification.yml', $ymlDump);
            }
            
            return new Response();
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
        if ('am' == $startHour->format('a')) { // For Morning shift
            /**
             * ALGORITHM FOR MORNING SHIFT.
             */
            $tmpPresence = $manager->getRepository(UserPresence::class)->createQueryBuilder('up')
                ->where('up.userId = :userId')
                ->andWhere('up.createdAt LIKE :givenDate')
                ->setParameter('userId', $user->getId())
                ->setParameter('givenDate', '%'.$dateNow->format('Y-m-d').'%')->getQuery()->getResult();

            if (0 == count($tmpPresence) && $dateNow < $startHour->add(new \DateInterval('PT1H'))) {
                // normal presence for job start
                $presenceData->setState(-1);
            } elseif (0 == count($tmpPresence) && $dateNow > $startHour->add(new \DateInterval('PT6H'))) {
                // if user forget to input when job start
                $presenceData->setState(1);
            } elseif (1 == count($tmpPresence)) { // normal presence for job done
                $presenceData->setState(1);
            } else {
                return 'Unknown error detected';
            }
        } elseif ('pm' == $startHour->format('a')) {
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
                ->setParameter('givenDate', '%'.$dateNow->format('Y-m-d').'%')->getQuery()->getResult();
            $toleranceStart = $startTime->sub(new \DateInterval('PT1H'));
            $toleranceEnd = $startTime->add(new \DateInterval('PT15M'));
            if (0 == count($tmpPresence)) { // NORMAL FOR JOB START
                if ($dateNow > $toleranceStart && $dateNow < $toleranceEnd) {
                    $presenceData->setState(-1);
                } elseif ($dateNow >= \DateTime::createFromFormat('H:i a', $shift->getEndTime()->format('H:i a'))) {
                    $presenceData->setState(1); // NORMAL FOR JOB DONE
                }
            } elseif (1 == count($tmpPresence)) {
                if (1 == $tmpPresence->getState()) {
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
        $value = Yaml::parse(file_get_contents(dirname(__DIR__).'/menu.yml'));

        return new Response(var_dump($value));
    }

    public function legacyChecklogAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        if ('POST' === $request->getMethod()) {
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

            return new JsonResponse(Debug::dump($shift));

//            $manager->persist($presenceData);
//            $manager->flush();
        }

        $data = $manager->createQuery(
            'SELECT job from OfficeBundle:UserJob job where job.shift is not NULL'
        );

        return $this->render('OfficeBundle:presence:legacy.html.twig', [
            'data' => $data->getResult(),
        ]);
    }
}
