<?php

namespace OfficeBundle\Repository;

use Doctrine\ORM\EntityRepository;
use OfficeBundle\Contracts\Repository\CompanyProfileInterface;
use OfficeBundle\Entity\CompanyProfile;

/**
 * CompanyProfileRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CompanyProfileRepository extends EntityRepository implements CompanyProfileInterface
{
    /**
     * @param $id
     *
     * @return CompanyProfile
     */
    public function findById($id)
    {
        return $this->find($id);
    }

    /**
     * @param $kodePerusahaan
     *
     * @return CompanyProfile
     */
    public function findByKodePerusahaan($kodePerusahaan)
    {
        return $this->findOneBy(['kodePerusahaan' => $kodePerusahaan]);
    }
}
