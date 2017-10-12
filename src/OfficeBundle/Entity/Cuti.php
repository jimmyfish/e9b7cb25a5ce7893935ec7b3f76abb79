<?php

namespace OfficeBundle\Entity;

/**
 * Cuti.
 */
class Cuti
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
    private $tanggal;

    /**
     * @var int
     */
    private $bulan;

    /**
     * @var int
     */
    private $tahun;

    /**
     * @var int
     */
    private $isValidated = 0;

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
     * @param UserPersonal $userId
     *
     * @return $this
     */
    public function setUserId(UserPersonal $userId)
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
     * Set tanggal.
     *
     * @param int $tanggal
     *
     * @return Cuti
     */
    public function setTanggal($tanggal)
    {
        $this->tanggal = $tanggal;

        return $this;
    }

    /**
     * Get tanggal.
     *
     * @return int
     */
    public function getTanggal()
    {
        return $this->tanggal;
    }

    /**
     * Set bulan.
     *
     * @param int $bulan
     *
     * @return Cuti
     */
    public function setBulan($bulan)
    {
        $this->bulan = $bulan;

        return $this;
    }

    /**
     * Get bulan.
     *
     * @return int
     */
    public function getBulan()
    {
        return $this->bulan;
    }

    /**
     * Set tahun.
     *
     * @param int $tahun
     *
     * @return Cuti
     */
    public function setTahun($tahun)
    {
        $this->tahun = $tahun;

        return $this;
    }

    /**
     * Get tahun.
     *
     * @return int
     */
    public function getTahun()
    {
        return $this->tahun;
    }

    /**
     * @param $isValidated
     *
     * @return $this
     */
    public function setIsvalidated($isValidated)
    {
        $this->isValidated = $isValidated;

        return $this;
    }

    /**
     * @return int
     */
    public function getIsValidated()
    {
        return $this->isValidated;
    }

    private $absDate;

    /**
     * @return mixed
     */
    public function getAbsDate()
    {
        return $this->absDate;
    }

    /**
     * @param mixed $absDate
     *
     * @return self
     */
    public function setAbsDate($absDate)
    {
        $this->absDate = $absDate;

        return $this;
    }

    private $validatedBy;

    /**
     * @return mixed
     */
    public function getValidatedBy()
    {
        return $this->validatedBy;
    }

    /**
     * @param mixed $validatedBy
     */
    public function setValidatedBy($validatedBy)
    {
        $this->validatedBy = $validatedBy;
    }

    private $description;

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    private $type = null;

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType(DayType $type)
    {
        $this->type = $type;
    }

    /**
     * @var String
     */
    private $hashDate;

    /**
     * @return String
     */
    public function getHashDate()
    {
        return $this->hashDate;
    }

    /**
     * @param UserPersonal $user
     * @param $date
     * @return $this
     */
    public function setHashDate(UserPersonal $user, $date)
    {
        $arrJson = json_encode([$user->getId(), $date]);

        $this->hashDate = base64_encode($arrJson);

        return $this;
    }

    private $dayGroup;

    /**
     * Get the value of Day Group
     *
     * @return mixed
     */
    public function getDayGroup()
    {
        return $this->dayGroup;
    }

    /**
     * Set the value of Day Group
     *
     * @param mixed dayGroup
     *
     * @return self
     */
    public function setDayGroup($dayGroup)
    {
        $this->dayGroup = $dayGroup;

        return $this;
    }

}
