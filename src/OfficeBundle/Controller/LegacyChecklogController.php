<?php
/**
 * Created by PhpStorm.
 * User: Fish
 * Date: 1/5/2018
 * Time: 7:14 PM
 */

namespace OfficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Components\HttpFoundation\Request;

class LegacyChecklogController extends Controller
{
    public function indexAction(Request $request)
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