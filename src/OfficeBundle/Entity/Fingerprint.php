<?php

namespace OfficeBundle\Entity;

/**
 * Fingerprint.
 */
class Fingerprint
{
    /**
     * @var int
     */
    private $id;

    private $userId;

    /**
     * @var int
     */
    private $fingerStatus;

    /**
     * @var string
     */
    private $fingerData;

    private $fingerSalt;

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
     * Set fingerStatus.
     *
     * @param int $fingerStatus
     *
     * @return Fingerprint
     */
    public function setFingerStatus($fingerStatus)
    {
        $this->fingerStatus = $fingerStatus;

        return $this;
    }

    /**
     * Get fingerStatus.
     *
     * @return int
     */
    public function getFingerStatus()
    {
        return $this->fingerStatus;
    }

    /**
     * Set fingerData.
     *
     * @param string $fingerData
     *
     * @return Fingerprint
     */
    public function setFingerData($fingerData)
    {
        $this->fingerData = $fingerData;

        return $this;
    }

    /**
     * Get fingerData.
     *
     * @return string
     */
    public function getFingerData()
    {
        return $this->fingerData;
    }

    /**
     * Set the value of Id.
     *
     * @param int id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of User Id.
     *
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set the value of User Id.
     *
     * @param mixed userId
     *
     * @return self
     */
    public function setUserId(UserPersonal $userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFingerSalt()
    {
        return $this->fingerSalt;
    }

    /**
     * @param mixed $fingerSalt
     */
    public function setFingerSalt($fingerSalt)
    {
        $this->fingerSalt = $fingerSalt;
    }
}
