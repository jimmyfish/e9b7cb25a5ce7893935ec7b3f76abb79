<?php

namespace OfficeBundle\Entity;

/**
 * DayType.
 */
class DayType
{
    /**
     * @var int
     */
    private $id;

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
     * Set data.
     *
     * @param string $data
     *
     * @return DayType
     */
    public function setData($data)
    {
        $this->data = json_encode($data);

        return $this;
    }

    /**
     * Get data.
     *
     * @return string
     */
    public function getData()
    {
        return json_decode($this->data);
    }

    private $name;

    private $count;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param mixed $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }

    private $isDeleted;

    /**
     * @return mixed
     */
    public function getisDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * @param mixed $isDeleted
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;
    }
}
