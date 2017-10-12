<?php

/**
 * Created by PhpStorm.
 * User: dzaki
 * Date: 02/05/17
 * Time: 15:58.
 */

namespace OfficeBundle\Contracts\Repository;

use OfficeBundle\Entity\UserJob;

interface UserJobInterface
{
    /**
     * @param $id
     *
     * @return UserJob
     */
    public function findById($id);

    /**
     * @param $userId
     *
     * @return UserJob
     */
    public function findByUserId($userId);
}
