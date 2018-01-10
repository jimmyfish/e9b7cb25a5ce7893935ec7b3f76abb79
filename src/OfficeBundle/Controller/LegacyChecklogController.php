<?php
/**
 * Created by PhpStorm.
 * User: Fish
 * Date: 1/5/2018
 * Time: 7:14 PM.
 */

namespace OfficeBundle\Controller;

use OfficeBundle\Entity\Shift;
use OfficeBundle\Entity\UserPersonal;
use OfficeBundle\Entity\UserPresence;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LegacyChecklogController extends Controller
{
    public function indexAction(Request $request)
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
            $startHour = new \DateTimeImmutable($shift->getStartTime()->format('H:i'));
            $startStmp = strtotime($shift->getStartTime()->format('H:i'));
            $endHour = new \DateTimeImmutable($shift->getEndTime()->format('H:i'));
            $endStmp = strtotime($shift->getEndTime()->format('H:i'));

            if ($endStmp < $startStmp) {
                $endStmp = $endStmp + (24 * 3600);
            }

            $interval = round(abs($endStmp - $startStmp) / 3600, 2);

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

                    $this->addFlash(
                        'presence_success',
                        'Absensi anda pada '.$dateNow->format('H:i').
                        ' kami terima.'
                    );

                    if ($dateNow > $endTime) {
                        $presenceData->setState(1);
                        $this->addFlash(
                            'presence_success',
                            'Absensi anda pada '.$dateNow->format('H:i').
                            ' kami terima, namun anda lupa checklog masuk'
                        );
                    }
                } elseif (1 == count($tmpPresence)) {
                    $presenceData->setState(1);
                    $this->addFlash(
                        'presence_success',
                        'Absensi anda pada '.$dateNow->format('H:i').
                        ' kami terima.'
                    );

                    if ($dateNow < $endTime && $dateNow > $startHour->add(new \DateInterval('PT'.$interval.'H'))) {
                        $this->addFlash(
                            'presence_info',
                            'Memutuskan untuk pulang lebih awal'
                        );
                    } else {
                        $this->addFlash(
                            'presence_info',
                            'Data anda telah kami terima sebelumnya'
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
                $flash = [];

                if ('am' == $startHour->format('a')) { // Absensi Shift 1
                    if (0 == count($tmpPresence)) {
                        /*
                         * Datang Normal
                         */
                        if ($dateNow < $startHour->add(new \DateInterval('PT15M'))) {
                            $presenceData->setState(-1);
                            $this->addFlash(
                                'presence_success',
                                'Absensi kamu pada '.$dateNow->format('H:i').
                                ' kami terima.'
                            );
                        } elseif ($dateNow > $startHour && $dateNow < $endTime) {
                            /*
                             * Masuk Terlambat
                             */
                            if ($dateNow < $endTime) {
                                $presenceData->setState(-1);
                                $presenceData->setDescription('Terlambat');
                                $this->addFlash(
                                    'presence_info',
                                    'Absensi kamu pada '.$dateNow->format('H:i').
                                    ' kami terima, tetapi dengan status terlambat'
                                );
                            }
                        } elseif ($dateNow > $startHour && $dateNow < $startHour->add(new \DateInterval('PT'.$interval.'H'))) {
                        }
                    } else {
                        /*
                         * Pulang Normal
                         */
                        if ($dateNow > $endTime) {
                            $presenceData->setState(1);
                            $this->addFlash(
                                'presence_success',
                                'Absensi kamu pada '.$dateNow->format('H:i').
                                ' kami terima'
                            );
                        }
                    }
                } elseif ('pm' == $startHour->format('a')) {
                    if ($endTime->format('G') < '12') {
                        /*
                         * Filtering for cross-day presence shift.
                         * Midnight start and daylight end.
                         */
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
                                    'presence_success' => 'Absensi kamu pada '.$dateNow->format('H:i').
                                        ' kami terima',
                                );
                            }

                            if ($dateNow > $startHour && $dateNow < $startHour->add(new \DateInterval('PT15M'))) {
                                // Statement 2
                                $presenceData->setState(-1);
                                $flash = array(
                                    'presence_success' => 'Absensi kamu pada '.$dateNow->format('H:i').
                                        ' kami terima',
                                );
                            }

                            if ($dateNow > $startHour->add(new \DateInterval('PT15M'))) {
                                if ($dateNow < $startHour->add(new \DateInterval('PT'.$interval.'H'))) {
                                    // Statement 3
                                    $presenceData->setState(-1);
                                    $presenceData->setDescription('MASUK TERLAMBAT');
                                    $flash = array(
                                        'presence_info' => 'Absensi kamu pada '.$dateNow->format('H:i').
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
                                        'presence_info' => 'Absensi kamu pada '.$dateNow->format('H:i').
                                            ' kami terima, pulang lebih awal dan lupa checklog masuk',
                                    );
                                }
                            }
                        } else {
                            if ($dateNow < $endTime) {
                                // Statement 5
                                $presenceData->setState(1);
                                $flash = array(
                                    'presence_info' => 'Memutuskan pulang lebih awal ?. Absensi kamu pada '.$dateNow->format('H:i').
                                        ' kami terima.',
                                );
                            }

                            if ($dateNow >= $endTime) {
                                // Statement 6
                                $presenceData->setState(1);
                                $flash = array(
                                    'presence_success' => 'Absensi kamu pada '.$dateNow->format('H:i').
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

                foreach ($flash as $key => $value) {
                    $this->addFlash(
                        $key,
                        $value
                    );
                }
            } catch (\Exception $e) {
                $this->addFlash(
                    'presence_error',
                    'Data anda tidak terekam, silahkan hubungi admin'
                );
            }
        }

        $data = $manager->createQuery(
            'SELECT job from OfficeBundle:UserJob job where job.shift is not NULL'
        );

        return $this->render('OfficeBundle:presence:legacy.html.twig', [
            'data' => $data->getResult(),
        ]);
    }
}
