<?php

namespace OfficeBundle\Entity;

/**
 * Anak.
 */
class Anak
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
     * @var string
     */
    private $nama;

    /**
     * @var \DateTime
     */
    private $tanggalLahir;

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
     * Set userId.
     *
     * @param int $userId
     *
     * @return Anak
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set nama.
     *
     * @param string $nama
     *
     * @return Anak
     */
    public function setNama($nama)
    {
        $this->nama = $nama;

        return $this;
    }

    /**
     * Get nama.
     *
     * @return string
     */
    public function getNama()
    {
        return $this->nama;
    }

    /**
     * Set tanggalLahir.
     *
     * @param \DateTime $tanggalLahir
     *
     * @return Anak
     */
    public function setTanggalLahir($tanggalLahir)
    {
        $this->tanggalLahir = $tanggalLahir;

        return $this;
    }

    /**
     * Get tanggalLahir.
     *
     * @return \DateTime
     */
    public function getTanggalLahir()
    {
        return $this->tanggalLahir;
    }
}
