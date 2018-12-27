<?php

namespace OfficeBundle\Controller\DayOff;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Common\Util\Debug;
use Symfony\Component\HttpFoundation\Response;

class DayOffListController extends Controller
{
    public function listSpecialAction()
    {
        $manager = $this->getDoctrine()->getManager();

        $data = $manager->createQueryBuilder()
                    ->select('c')
                    ->from('OfficeBundle:Cuti', 'c')
                    ->where('c.description LIKE :condition')
                    ->setParameter('condition', 'Cuti Bersama%')
                    ->getQuery()->getResult();

        return $this->render('OfficeBundle:dayoff:list-special.html.twig', [
            'data' => $data,
        ]);
    }
}
