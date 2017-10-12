<?php

namespace OfficeBundle\Entity;

/**
 * UserFamily.
 */
class UserFamily
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var int
     */
    private $statusPerkawinan;

    /**
     * @var string
     */
    private $pasangan;

    /**
     * @var string
     */
    private $orangTua;

    /**
     * @var string
     */
    private $alamatOrangTua;

    /**
     * @var string
     */
    private $mertua;

    /**
     * @var string
     */
    private $alamatMertua;

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

    /**
     * Set statusPerkawinan.
     *
     * @param int $statusPerkawinan
     *
     * @return UserFamily
     */
    public function setStatusPerkawinan($statusPerkawinan)
    {
        $this->statusPerkawinan = $statusPerkawinan;

        return $this;
    }

    /**
     * Get statusPerkawinan.
     *
     * @return int
     */
    public function getStatusPerkawinan()
    {
        return $this->statusPerkawinan;
    }

    /**
     * Set pasangan.
     *
     * @param string $pasangan
     *
     * @return UserFamily
     */
    public function setPasangan($pasangan)
    {
        $this->pasangan = $pasangan;

        return $this;
    }

    /**
     * Get pasangan.
     *
     * @return string
     */
    public function getPasangan()
    {
        return $this->pasangan;
    }

    /**
     * Set orangTua.
     *
     * @param string $orangTua
     *
     * @return UserFamily
     */
    public function setOrangTua($orangTua)
    {
        $this->orangTua = $orangTua;

        return $this;
    }

    /**
     * Get orangTua.
     *
     * @return string
     */
    public function getOrangTua()
    {
        return $this->orangTua;
    }

    /**
     * Set alamatOrangTua.
     *
     * @param string $alamatOrangTua
     *
     * @return UserFamily
     */
    public function setAlamatOrangTua($alamatOrangTua)
    {
        $this->alamatOrangTua = $alamatOrangTua;

        return $this;
    }

    /**
     * Get alamatOrangTua.
     *
     * @return string
     */
    public function getAlamatOrangTua()
    {
        return $this->alamatOrangTua;
    }

    /**
     * Set mertua.
     *
     * @param string $mertua
     *
     * @return UserFamily
     */
    public function setMertua($mertua)
    {
        $this->mertua = $mertua;

        return $this;
    }

    /**
     * Get mertua.
     *
     * @return string
     */
    public function getMertua()
    {
        return $this->mertua;
    }

    /**
     * Set alamatMertua.
     *
     * @param string $alamatMertua
     *
     * @return UserFamily
     */
    public function setAlamatMertua($alamatMertua)
    {
        $this->alamatMertua = $alamatMertua;

        return $this;
    }

    /**
     * Get alamatMertua.
     *
     * @return string
     */
    public function getAlamatMertua()
    {
        return $this->alamatMertua;
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

    /**
     * Gets the value of userId.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Sets the value of userId.
     *
     * @param int $userId the user id
     *
     * @return self
     */
    public function setUserId(UserPersonal $userId)
    {
        $this->userId = $userId;

        return $this;
    }
}
