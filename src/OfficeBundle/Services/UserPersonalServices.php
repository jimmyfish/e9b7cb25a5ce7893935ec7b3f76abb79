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

        if ($kampret == 0) {
            $user->setStatus(1);
        } elseif ($kampret == 1) {
            $user->setStatus(2);
        } else {
            $user->setStatus(0);
        }
    }

    public static function changeValidate(UserPersonal $user)
    {
        $kampret = $user->getIsValidated();

        if ($kampret == 1) {
            $user->setIsValidated(0);
        } else {
            $user->setIsValidated(1);
        }
    }

    public static function changeActive(UserPersonal $user)
    {
        $kampret = $user->getIsActive();

        if ($kampret == 1) {
            $user->setIsActive(0);
        } else {
            $user->setIsActive(1);
        }
    }
}
