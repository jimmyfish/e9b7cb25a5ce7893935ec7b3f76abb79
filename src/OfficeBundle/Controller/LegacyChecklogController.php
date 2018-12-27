<?php
/**
 * Created by PhpStorm.
 * User: Fish
 * Date: 1/5/2018
 * Time: 7:14 PM.
 */

namespace OfficeBundle\Controller;

use Doctrine\Common\Util\Debug;
use OfficeBundle\Entity\Shift;
use OfficeBundle\Entity\UserPersonal;
use OfficeBundle\Entity\UserPresence;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LegacyChecklogController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse|Response
     * @throws \Exception
     */
    public function indexAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        if ('POST' === $request->getMethod()) {
            $userPersonalRepository = $manager->getRepository(UserPersonal::class);
            $userPresenceRepository = $manager->getRepository(UserPresence::class);

            $userId = $request->get('user_id');
            $userPersonal = $userPersonalRepository->find($userId);

            if ($userPersonal instanceof UserPersonal) {
                $presence = UserPresence::createDefault($userPersonal, new \DateTime());

                $shift = $userPersonal->getJob()->getShift();

                $dateNow = new \DateTime();
                $beforeInterval = new \DateInterval("PT1H");
                $beforeInterval->invert = 1;
                $afterInterval = new \DateInterval("PT1H");

                $oneHourAfter = clone $dateNow;
                $oneHourAfter->add($afterInterval);

                $oneHourBefore = clone $dateNow;
                $oneHourBefore->add($beforeInterval);

                /**
                 * Let's Assume :
                 * 1. Office's Shift -> 08:00 - 16:00 { So, it's AM to PM }
                 * 2. Non-Office's First Shift -> 06:00 - 14:00 { So, it's AM to PM }
                 * 3. Non-Office's Second Shift -> 14:00 - 22:00 { So, it's PM to PM }
                 * 4. Non-Office's Third Shift -> 22:00 - 06:00 { So, it's PM to AM }
                 *
                 * So, we don't need to add any switch on AM's case, because AM's Employee always come home at PM time, Right ?
                 */
                switch (date('A', strtotime($shift->getStartTime()->format('H:i')))) {
                    case "AM":
                        if (date('A') === "AM") {
                            $presence->setState(-1);

                            if (date("H:i" > $oneHourAfter)) {
                                $presence->setDescription("Terlambat Masuk");
                            }
                        } else {
                            $tmpPresence = $userPresenceRepository->createQueryBuilder('p')
                                ->where('p.userId = :userId')
                                ->andWhere('p.date LIKE :date')
                                ->setParameter('userId', $userId)
                                ->setParameter('date', date('Y-m-d') . '%');

                            $presence->setState(1);

                            if (count($tmpPresence) == 0) {
                                $presence->setDescription("Lupa checklog Masuk");
                            }

                            if (date('H:i') < $oneHourBefore) {
                                $presence->setDescription("Pulang Lebih Awal");
                            }
                        }
                        break;
                    case "PM":
                        switch (date('A', strtotime($shift->getEndTime()->format('H:i')))) {
                            case "AM":
                                if (date('A') == "AM") {
                                    $negativeInterval = new \DateInterval("P1D");
                                    $negativeInterval->invert = 1;

                                    $aDayBefore = new \DateTime();
                                    $aDayBefore->add($negativeInterval);

                                    # Find presence a day before
                                    $tmpPresence = $userPresenceRepository->createQueryBuilder('p')
                                        ->where('p.userId = :userId')->andWhere('p.date LIKE :date')
                                        ->setParameter('userId', $userId)->setParameter('date', $aDayBefore->format('Y-m-d')."%")->getQuery()->getResult();

                                    $presence->setState(1);

                                    if (count($tmpPresence) == 0) {
                                        $presence->setDescription("Lupa checklog masuk");
                                    }
                                } else {
                                    $presence->setState(-1);
                                }
                                break;
                            case "PM":
                                $tmpPresence = $userPresenceRepository->createQueryBuilder('p')
                                    ->where('p.userId = :userId')->where('p.date LIKE :date')
                                    ->setParameter('userId', $userId)->setParameter('date', date('Y-m-d')."%")->getQuery()->getResult();

                                $negativeTimeInterval = new \DateInterval("PT2H");
                                $negativeTimeInterval->invert = 1;

                                $newTime = new \DateTime($shift->getEndTime()->format('H:i'));
                                $newTime->add($negativeTimeInterval);

                                # Check if present time whether start or end time
                                if (date('H:i') > $newTime->format('H:i')) {
                                    $presence->setState(1);

                                    if (count($tmpPresence) == 0) {
                                        $presence->setDescription("Lupa checklog Masuk");
                                    }
                                } else {
                                    $presence->setState(-1);
                                }
                                break;
                            default:
                        }
                        break;
                    default:
                }
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
