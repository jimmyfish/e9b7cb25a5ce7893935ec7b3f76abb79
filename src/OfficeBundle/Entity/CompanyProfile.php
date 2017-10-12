<?php

namespace OfficeBundle\Entity;

use Symfony\Component\HttpFoundation\Request;

/**
 * CompanyProfile.
 */
class CompanyProfile
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $namaPerusahaan;

    /**
     *@var int
     */
    private $isDeleted;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public static function create($namaPerusahaan)
    {
        $company = new self();
        $company->setNamaPerusahaan($namaPerusahaan);
        $company->setIsDeleted(0);

        return $company;
    }

    public function createCompany(Request $request)
    {
        $namaPerusahaan = $request->get('nama-perusahaan');

        $createCompany = self::create($namaPerusahaan);

        return $createCompany;
    }

    /**
     * Set namaPerusahaan.
     *
     * @param string $namaPerusahaan
     *
     * @return CompanyProfile
     */
    public function setNamaPerusahaan($namaPerusahaan)
    {
        $this->namaPerusahaan = $namaPerusahaan;

        return $this;
    }

    /**
     * Get namaPerusahaan.
     *
     * @return string
     */
    public function getNamaPerusahaan()
    {
        return $this->namaPerusahaan;
    }

    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    public function getIsDeleted()
    {
        return $this->isDeleted;
    }
}
