<?php

namespace OfficeBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OfficeBundle\Entity\UserPersonal;

class UserPresenceData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user = $manager->getRepository(UserPersonal::class)->find(1);
        $date = new \DateTime();
        $presence = new UserPresence();
        $presence->setUserId($user);
        $presence->setMonth($date->format('m'));
        $presence->setYear($date->format('Y'));
        $presence->setData([1 => 0, 2 => 1, 3 => 0]);
        $presence->setIsValidated(1);
        $presence->setIsDeleted(0);

        $manager->persist($presence);
        $manager->flush();
    }
}
