<?php

/**
 * Created by PhpStorm.
 * User: dzaki
 * Date: 12/05/17
 * Time: 16:28.
 */

namespace OfficeBundle\Services;

use Nelmio\Alice\fixtures\User;
use OfficeBundle\Entity\UserPersonal;

class UserPersonalServices
{
    public static function changeStatus(UserPersonal $user)
    {
        $kampret = $user->getStatus();

        if (0 == $kampret) {
            $user->setStatus(1);
        } elseif (1 == $kampret) {
            $user->setStatus(2);
        } else {
            $user->setStatus(0);
        }
    }

    public static function changeValidate(UserPersonal $user)
    {
        $kampret = $user->getIsValidated();

        if (1 == $kampret) {
            $user->setIsValidated(0);
        } else {
            $user->setIsValidated(1);
        }
    }

    public static function changeActive(UserPersonal $user)
    {
        $kampret = $user->getIsActive();

        if (1 == $kampret) {
            $user->setIsActive(0);
        } else {
            $user->setIsActive(1);
        }
    }
}
