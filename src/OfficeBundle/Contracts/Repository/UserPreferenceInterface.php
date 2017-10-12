<?php

/**
 * Created by PhpStorm.
 * User: dzaki
 * Date: 05/05/17
 * Time: 13:39.
 */

namespace OfficeBundle\Contracts\Repository;

use OfficeBundle\Entity\UserPreference;

interface UserPreferenceInterface
{
    /**
     * @param $id
     *
     * @return UserPreference
     */
    public function findById($id);

    /**
     * @param $userId
     *
     * @return UserPreference
     */
    public function findByUserId($userId);
}
