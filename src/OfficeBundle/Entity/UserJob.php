<?php

namespace OfficeBundle\Entity;

/**
 * UserJob.
 */
class UserJob
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
    private $golongan;

    /**
     * @var string
     */
    private $jenjangPangkat;

    /**
     * @var string
     */
    private $jabatan;

    /**
     * @var \DateTime
     */
    private $tanggalMasuk;

    /**
     * @var int
     */
    private $statusKaryawan;

    /**
     * @var string
     */
    private $pengalamanKerjaTerakhir;

    /**
     * @var string
     */
    private $kontrakTraining;

    /**
     * @var string
     */
    private $kontrakKerja;

    /**
     * @var \DateTime
     */
    private $tanggalPercobaan;

    /**
     * @var \DateTime
     */
    private $tanggalSkTetap;

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
     * Set golongan.
     *
     * @param string $golongan
     *
     * @return UserJob
     */
    public function setGolongan($golongan)
    {
        $this->golongan = $golongan;

        return $this;
    }

    /**
     * Get golongan.
     *
     * @return string
     */
    public function getGolongan()
    {
        return $this->golongan;
    }

    /**
     * Set jenjangPangkat.
     *
     * @param string $jenjangPangkat
     *
     * @return UserJob
     */
    public function setJenjangPangkat($jenjangPangkat)
    {
        $this->jenjangPangkat = $jenjangPangkat;

        return $this;
    }

    /**
     * Get jenjangPangkat.
     *
     * @return string
     */
    public function getJenjangPangkat()
    {
        return $this->jenjangPangkat;
    }

    /**
     * Set jabatan.
     *
     * @param string $jabatan
     *
     * @return UserJob
     */
    public function setJabatan($jabatan)
    {
        $this->jabatan = $jabatan;

        return $this;
    }

    /**
     * Get jabatan.
     *
     * @return string
     */
    public function getJabatan()
    {
        return $this->jabatan;
    }

    /**
     * Set tanggalMasuk.
     *
     * @param \DateTime $tanggalMasuk
     *
     * @return UserJob
     */
    public function setTanggalMasuk($tanggalMasuk)
    {
        $this->tanggalMasuk = new \DateTime($tanggalMasuk);

        return $this;
    }

    /**
     * Get tanggalMasuk.
     *
     * @return \DateTime
     */
    public function getTanggalMasuk()
    {
        return $this->tanggalMasuk;
    }

    /**
     * Set statusKaryawan.
     *
     * @param int $statusKaryawan
     *
     * @return UserJob
     */
    public function setStatusKaryawan($statusKaryawan)
    {
        $this->statusKaryawan = $statusKaryawan;

        return $this;
    }

    /**
     * Get statusKaryawan.
     *
     * @return int
     */
    public function getStatusKaryawan()
    {
        return $this->statusKaryawan;
    }

    /**
     * Set pengalamanKerjaTerakhir.
     *
     * @param string $pengalamanKerjaTerakhir
     *
     * @return UserJob
     */
    public function setPengalamanKerjaTerakhir($pengalamanKerjaTerakhir)
    {
        $this->pengalamanKerjaTerakhir = $pengalamanKerjaTerakhir;

        return $this;
    }

    /**
     * Get pengalamanKerjaTerakhir.
     *
     * @return string
     */
    public function getPengalamanKerjaTerakhir()
    {
        return $this->pengalamanKerjaTerakhir;
    }

    /**
     * Set kontrakTraining.
     *
     * @param string $kontrakTraining
     *
     * @return UserJob
     */
    public function setKontrakTraining($kontrakTraining)
    {
        $this->kontrakTraining = $kontrakTraining;

        return $this;
    }

    /**
     * Get kontrakTraining.
     *
     * @return string
     */
    public function getKontrakTraining()
    {
        return $this->kontrakTraining;
    }

    /**
     * Set kontrakKerja.
     *
     * @param string $kontrakKerja
     *
     * @return UserJob
     */
    public function setKontrakKerja($kontrakKerja)
    {
        $this->kontrakKerja = $kontrakKerja;

        return $this;
    }

    /**
     * Get kontrakKerja.
     *
     * @return string
     */
    public function getKontrakKerja()
    {
        return $this->kontrakKerja;
    }

    /**
     * Set tanggalPercobaan.
     *
     * @param \DateTime $tanggalPercobaan
     *
     * @return UserJob
     */
    public function setTanggalPercobaan($tanggalPercobaan)
    {
        $this->tanggalPercobaan = new \DateTime($tanggalPercobaan);

        return $this;
    }

    /**
     * Get tanggalPercobaan.
     *
     * @return \DateTime
     */
    public function getTanggalPercobaan()
    {
        return $this->tanggalPercobaan;
    }

    /**
     * Set tanggalSkTetap.
     *
     * @param \DateTime $tanggalSkTetap
     *
     * @return UserJob
     */
    public function setTanggalSkTetap($tanggalSkTetap)
    {
        $this->tanggalSkTetap = new \DateTime($tanggalSkTetap);

        return $this;
    }

    /**
     * Get tanggalSkTetap.
     *
     * @return \DateTime
     */
    public function getTanggalSkTetap()
    {
        return $this->tanggalSkTetap;
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

    /**
     * Contain shift time of every employee.
     *
     * @var int
     */
    private $shift;

    /**
     * Gets the value of shift.
     *
     * @return mixed
     */
    public function getShift()
    {
        return $this->shift;
    }

    /**
     * Sets the value of shift.
     *
     * @param mixed $shift the shift
     *
     * @return self
     */
    public function setShift(Shift $shift)
    {
        $this->shift = $shift;

        return $this;
    }

    private $quotas = 6;

    /**
     * @return mixed
     */
    public function getQuotas()
    {
        return $this->quotas;
    }

    /**
     * @param mixed $quotas
     */
    public function setQuotas($quotas)
    {
        $this->quotas = $quotas;
    }
}
