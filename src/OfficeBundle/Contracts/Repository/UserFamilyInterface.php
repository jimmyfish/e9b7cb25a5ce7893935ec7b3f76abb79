<?php

/**
 * Created by PhpStorm.
 * User: dzaki
 * Date: 02/05/17
 * Time: 15:57.
 */

namespace OfficeBundle\Contracts\Repository;

use OfficeBundle\Entity\UserFamily;

interface UserFamilyInterface
{
    /**
     * @param $id
     *
     * @return UserFamily
     */
    public function findById($id);

    /**
     * @param $userId
     *
     * @return UserFamily
     */
    public function findByUserId($userId);
}
