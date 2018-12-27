<?php
/**
 * Created by PhpStorm.
 * User: afif
 * Date: 24/12/2017
 * Time: 7:54
 */

namespace OfficeBundle\EventListener;

use Doctrine\ORM\EntityManager;
use OfficeBundle\Entity\Attachment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ModalEventListener extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{

    protected $manager;

    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    public function getGlobals()
    {
        $qb = $this->manager->createQueryBuilder();

        $qb->select('u')->from(Attachment::class, 'u')->where('u.isValidated = 0');

        $data = $qb->getQuery()->getResult();

        $i = 0;

        foreach ($data as $item) {
            $results[$i]['username'] = $item->getUserId()->getUsername();
            $results[$i]['description'] = $item->getDescription();

            ++$i;
        }

        return array(
            'admin' => $results
        );
    }

//    private function notifAdmin()
//    {
//        $qb = $this->manager->createQueryBuilder();
//
//        $qb->select('u')->from(Attachment::class, 'u')->where('u.isValidated = 0');
//
//        $data = $qb->getQuery()->getResult();
//
//        $i = 0;
//
//        foreach ($data as $item) {
//            $results[$i]['username'] = $item->getUserId()->getUsername();
//            $results[$i]['description'] = $item->getDescription();
//
//            ++$i;
//        }
//
//        return $results;
//    }
}
