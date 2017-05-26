<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ec2InstanceHashrate
 *
 * @ORM\Table(name="ec2_instance_hashrate")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Ec2InstanceHashrateRepository")
 */
class Ec2InstanceHashrate
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Ec2Instance
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Ec2Instance", fetch="EAGER")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true, onDelete="cascade")
     */
    private $ec2Instance;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=63)
     */
    private $coinCode;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $hashrate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $time;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set coinCode
     *
     * @param string $coinCode
     *
     * @return Ec2InstanceHashrate
     */
    public function setCoinCode($coinCode)
    {
        $this->coinCode = $coinCode;

        return $this;
    }

    /**
     * Get coinCode
     *
     * @return string
     */
    public function getCoinCode()
    {
        return $this->coinCode;
    }

    /**
     * Set hashrate
     *
     * @param float $hashrate
     *
     * @return Ec2InstanceHashrate
     */
    public function setHashrate($hashrate)
    {
        $this->hashrate = $hashrate;

        return $this;
    }

    /**
     * Get hashrate
     *
     * @return float
     */
    public function getHashrate()
    {
        return $this->hashrate;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     *
     * @return Ec2InstanceHashrate
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set ec2Instance
     *
     * @param \AppBundle\Entity\Ec2Instance $ec2Instance
     *
     * @return Ec2InstanceHashrate
     */
    public function setEc2Instance(\AppBundle\Entity\Ec2Instance $ec2Instance = null)
    {
        $this->ec2Instance = $ec2Instance;

        return $this;
    }

    /**
     * Get ec2Instance
     *
     * @return \AppBundle\Entity\Ec2Instance
     */
    public function getEc2Instance()
    {
        return $this->ec2Instance;
    }
}
