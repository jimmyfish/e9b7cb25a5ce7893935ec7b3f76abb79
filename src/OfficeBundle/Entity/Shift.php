<?php

namespace OfficeBundle\Entity;

/**
 * Shift.
 */
class Shift
{
    /**
     * @var int
     */
    private $id;

    private $label;

    private $startTime;

    private $endTime;

    private $createdAt;

    private $updatedAt;

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
     * Gets the value of startTime.
     *
     * @return mixed
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Sets the value of startTime.
     *
     * @param mixed $startTime the start time
     *
     * @return self
     */
    public function setStartTime($startTime)
    {
        $this->startTime = new \DateTime($startTime);

        return $this;
    }

    /**
     * Gets the value of endTime.
     *
     * @return mixed
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Sets the value of endTime.
     *
     * @param mixed $endTime the end time
     *
     * @return self
     */
    public function setEndTime($endTime)
    {
        $this->endTime = new \DateTime($endTime);

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

    /**
     * Gets the value of updatedAt.
     *
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Sets the value of updatedAt.
     *
     * @param mixed $updatedAt the updated at
     *
     * @return self
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Gets the value of label.
     *
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Sets the value of label.
     *
     * @param mixed $label the label
     *
     * @return self
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }
}
