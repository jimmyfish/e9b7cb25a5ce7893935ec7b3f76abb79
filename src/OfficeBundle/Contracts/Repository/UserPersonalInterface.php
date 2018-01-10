<?php

/**
 * Created by PhpStorm.
 * User: dzaki
 * Date: 28/04/17
 * Time: 23:07.
 */

namespace OfficeBundle\Contracts\Repository;

use OfficeBundle\Entity\UserPersonal;

interface UserPersonalInterface
{
    /**
     * @param $id
     *
     * @return UserPersonal
     */
    public function findById($id);

    /**
     * @param $nik
     *
     * @return UserPersonal
     */
    public function findByNik($nik);

    /**
     * @param $username
     *
     * @return UserPersonal
     */
    public function findByUsername($username);

    /**
     * @param $email
     *
     * @return UserPersonal
     */
    public function findByEmail($email);

    /**
     * @param $penempatan
     *
     * @return UserPersonal
     */
    public function findByPenempatan($penempatan);
}
