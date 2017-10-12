<?php

namespace OfficeBundle\Entity;

/**
 * Device.
 */
class Device
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $deviceName;

    /**
     * @var string
     */
    private $sn;

    /**
     * @var string
     */
    private $ac;

    /**
     * @var string
     */
    private $vc;

    /**
     * @var string
     */
    private $vkey;

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
     * Set deviceName.
     *
     * @param string $deviceName
     *
     * @return Device
     */
    public function setDeviceName($deviceName)
    {
        $this->deviceName = $deviceName;

        return $this;
    }

    /**
     * Get deviceName.
     *
     * @return string
     */
    public function getDeviceName()
    {
        return $this->deviceName;
    }

    /**
     * Set sn.
     *
     * @param string $sn
     *
     * @return Device
     */
    public function setSn($sn)
    {
        $this->sn = $sn;

        return $this;
    }

    /**
     * Get sn.
     *
     * @return string
     */
    public function getSn()
    {
        return $this->sn;
    }

    /**
     * Set ac.
     *
     * @param string $ac
     *
     * @return Device
     */
    public function setAc($ac)
    {
        $this->ac = $ac;

        return $this;
    }

    /**
     * Get ac.
     *
     * @return string
     */
    public function getAc()
    {
        return $this->ac;
    }

    /**
     * Set vc.
     *
     * @param string $vc
     *
     * @return Device
     */
    public function setVc($vc)
    {
        $this->vc = $vc;

        return $this;
    }

    /**
     * Get vc.
     *
     * @return string
     */
    public function getVc()
    {
        return $this->vc;
    }

    /**
     * Set vkey.
     *
     * @param string $vkey
     *
     * @return Device
     */
    public function setVkey($vkey)
    {
        $this->vkey = $vkey;

        return $this;
    }

    /**
     * Get vkey.
     *
     * @return string
     */
    public function getVkey()
    {
        return $this->vkey;
    }
}
