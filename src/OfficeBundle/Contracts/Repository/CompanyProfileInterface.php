<?php

/**
 * Created by PhpStorm.
 * User: dzaki
 * Date: 28/04/17
 * Time: 23:10.
 */

namespace OfficeBundle\Contracts\Repository;

use OfficeBundle\Entity\CompanyProfile;

interface CompanyProfileInterface
{
    /**
     * @param $id
     *
     * @return CompanyProfile
     */
    public function findById($id);

    /**
     * @param $kodePerusahaan
     *
     * @return CompanyProfile
     */
    public function findByKodePerusahaan($kodePerusahaan);
}
