<?php

namespace OfficeBundle\Entity;

/**
 * UserPresence.
 */
class UserPresence
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
    private $month;

    /**
     * @var int
     */
    private $year;

    /**
     * @var string
     */
    private $data;

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
     * Set month.
     *
     * @param int $month
     *
     * @return UserPresence
     */
    public function setMonth($month)
    {
        $this->month = $month;

        return $this;
    }

    /**
     * Get month.
     *
     * @return int
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * @param $year
     *
     * @return $this
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year.
     *
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data.
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    private $createdAt;

    private $day;

    /**
     * Gets the value of day.
     *
     * @return mixed
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Sets the value of day.
     *
     * @param mixed $day the day
     *
     * @return self
     */
    public function setDay($day)
    {
        $this->day = $day;

        return $this;
    }

    /**
     * Gets the value of createdAt.
     *
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Sets the value of createdAt.
     *
     * @param mixed $createdAt the created at
     *
     * @return self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    private $state;

    /**
     * Gets the value of state.
     *
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Sets the value of state.
     *
     * @param mixed $state the state
     *
     * @return self
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    private $shift;

    /**
     * @return mixed
     */
    public function getShift()
    {
        return $this->shift;
    }

    /**
     * @param mixed $shift
     */
    public function setShift(Shift $shift)
    {
        $this->shift = $shift;
    }

    public static function createDefault($user, $dateNow, Shift $shift)
    {
        $data = new self();
        $data->setUserId($user);
        $data->setDay($dateNow->format('d'));
        $data->setMonth($dateNow->format('m'));
        $data->setYear($dateNow->format('Y'));
        $data->setData(1);
        $data->setShift($shift);
        $data->setCreatedAt($dateNow);

        return $data;
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
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    private $absoluteDay;

    /**
     * @return mixed
     */
    public function getAbsoluteDay()
    {
        return $this->absoluteDay;
    }

    /**
     * @param mixed $absoluteDay
     */
    public function setAbsoluteDay($absoluteDay)
    {
        $this->absoluteDay = $absoluteDay;
    }
}
