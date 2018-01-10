<?php

namespace OfficeBundle\Entity;

/**
 * Attachment.
 */
class Attachment
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
    private $typeForm;

    /**
     * @var int
     */
    private $absen;

    /**
     * @var string
     */
    private $description;

    /**
     * @var \DateTime
     */
    private $tglMulai;

    /**
     * @var \DateTime
     */
    private $tglAkhir;

    /**
     * @var string
     */
    private $hashDate;

    /**
     * @var int
     */
    private $isValidated;

    /**
     * @var int
     */
    private $isDeleted;

    /**
     * @var \DateTime
     */
    private $createdAt;

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
     * Set user.
     *
     * @param int $userId
     *
     * @return Attachment
     */
    public function setUserId(UserPersonal $userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get user.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param $typeForm
     *
     * @return Attachment
     */
    public function setTypeForm($typeForm)
    {
        $this->typeForm = $typeForm;

        return $this;
    }

    /**
     * @return int
     */
    public function getTypeForm()
    {
        return $this->typeForm;
    }

    /**
     * @param $absen
     *
     * @return Attachment
     */
    public function setAbsen($absen)
    {
        $this->absen = $absen;

        return $this;
    }

    /**
     * @return int
     */
    public function getAbsen()
    {
        return $this->absen;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Attachment
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param $tglMulai
     *
     * @return Attachment
     */
    public function setTglMulai($tglMulai)
    {
        $this->tglMulai = new \DateTime($tglMulai);

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTglMulai()
    {
        return $this->tglMulai;
    }

    /**
     * @param $tglAkhir
     *
     * @return Attachment
     */
    public function setTglAkhir($tglAkhir)
    {
        $this->tglAkhir = new \DateTime($tglAkhir);

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTglAkhir()
    {
        return $this->tglAkhir;
    }

    /**
     * @return string
     */
    public function getHashDate()
    {
        return $this->hashDate;
    }

    /**
     * @param UserPersonal $user
     * @param $date
     *
     * @return $this
     */
    public function setHashDate(UserPersonal $user, $dateStart, $dateFinish)
    {
        $arrJson = json_encode([$user->getId(), $dateStart, $dateFinish]);

        $this->hashDate = base64_encode($arrJson);

        return $this;
    }

    /**
     * Set isValidated.
     *
     * @param int $isValidated
     *
     * @return Attachment
     */
    public function setIsValidated($isValidated)
    {
        $this->isValidated = $isValidated;

        return $this;
    }

    /**
     * Get isValidated.
     *
     * @return int
     */
    public function getIsValidated()
    {
        return $this->isValidated;
    }

    /**
     * Set isDeleted.
     *
     * @param int $isDeleted
     *
     * @return Attachment
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Get isDeleted.
     *
     * @return int
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * @param $createdAt
     *
     * @return Attachment
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
