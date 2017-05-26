<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Ec2Instance
 *
 * @ORM\Table(name="ec2_instance")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Ec2InstanceRepository")
 */
class Ec2Instance
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
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $instanceId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $instanceTypeName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $amiId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $publicIpv4;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=1023)
     */
    private $publicHostname;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=512)
     */
    private $cpuModel;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $cpuCoreCount;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $cpuMicrocode;

    /**
     * @var integer
     *
     * @ORM\Column(type="bigint")
     */
    private $cpuFreq;

    /**
     * @var string
     *
     * @ORM\Column(type="integer")
     */
    private $cpuCacheSize;

    /**
     * @var float
     *
     * @ORM\Column(type="integer")
     */
    private $cpuBogomips;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $cpuAes;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $firstReportAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $lastReportAt;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $uptimeSeconds;

    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Ec2InstanceHashrateStats", mappedBy="ec2Instance", fetch="EAGER")
     */
    private $hashrateStats;


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
     * Set instanceId
     *
     * @param string $instanceId
     *
     * @return Ec2Instance
     */
    public function setInstanceId($instanceId)
    {
        $this->instanceId = $instanceId;

        return $this;
    }

    /**
     * Get instanceId
     *
     * @return string
     */
    public function getInstanceId()
    {
        return $this->instanceId;
    }

    /**
     * Set instanceTypeName
     *
     * @param string $instanceTypeName
     *
     * @return Ec2Instance
     */
    public function setInstanceTypeName($instanceTypeName)
    {
        $this->instanceTypeName = $instanceTypeName;

        return $this;
    }

    /**
     * Get instanceTypeName
     *
     * @return string
     */
    public function getInstanceTypeName()
    {
        return $this->instanceTypeName;
    }

    /**
     * Set publicIpv4
     *
     * @param string $publicIpv4
     *
     * @return Ec2Instance
     */
    public function setPublicIpv4($publicIpv4)
    {
        $this->publicIpv4 = $publicIpv4;

        return $this;
    }

    /**
     * Get publicIpv4
     *
     * @return string
     */
    public function getPublicIpv4()
    {
        return $this->publicIpv4;
    }

    /**
     * Set publicHostname
     *
     * @param string $publicHostname
     *
     * @return Ec2Instance
     */
    public function setPublicHostname($publicHostname)
    {
        $this->publicHostname = $publicHostname;

        return $this;
    }

    /**
     * Get publicHostname
     *
     * @return string
     */
    public function getPublicHostname()
    {
        return $this->publicHostname;
    }

    /**
     * Set cpuModel
     *
     * @param string $cpuModel
     *
     * @return Ec2Instance
     */
    public function setCpuModel($cpuModel)
    {
        $this->cpuModel = $cpuModel;

        return $this;
    }

    /**
     * Get cpuModel
     *
     * @return string
     */
    public function getCpuModel()
    {
        return $this->cpuModel;
    }

    /**
     * Set cpuMicrocode
     *
     * @param string $cpuMicrocode
     *
     * @return Ec2Instance
     */
    public function setCpuMicrocode($cpuMicrocode)
    {
        $this->cpuMicrocode = $cpuMicrocode;

        return $this;
    }

    /**
     * Get cpuMicrocode
     *
     * @return string
     */
    public function getCpuMicrocode()
    {
        return $this->cpuMicrocode;
    }

    /**
     * Set cpuFreq
     *
     * @param float $cpuFreq
     *
     * @return Ec2Instance
     */
    public function setCpuFreq($cpuFreq)
    {
        $this->cpuFreq = $cpuFreq;

        return $this;
    }

    /**
     * Get cpuFreq
     *
     * @return float
     */
    public function getCpuFreq()
    {
        return $this->cpuFreq;
    }

    /**
     * Set cacheSsize
     *
     * @param string $cpuCacheSize
     *
     * @return Ec2Instance
     */
    public function setCpuCacheSize($cpuCacheSize)
    {
        $this->cpuCacheSize = $cpuCacheSize;

        return $this;
    }

    /**
     * Get cacheSsize
     *
     * @return string
     */
    public function getCpuCacheSize()
    {
        return $this->cpuCacheSize;
    }

    /**
     * Set cpuBogomips
     *
     * @param float $cpuBogomips
     *
     * @return Ec2Instance
     */
    public function setCpuBogomips($cpuBogomips)
    {
        $this->cpuBogomips = $cpuBogomips;

        return $this;
    }

    /**
     * Get cpuBogomips
     *
     * @return float
     */
    public function getCpuBogomips()
    {
        return $this->cpuBogomips;
    }

    /**
     * Set cpuAes
     *
     * @param boolean $cpuAes
     *
     * @return Ec2Instance
     */
    public function setCpuAes($cpuAes)
    {
        $this->cpuAes = $cpuAes;

        return $this;
    }

    /**
     * Get cpuAes
     *
     * @return boolean
     */
    public function getCpuAes()
    {
        return $this->cpuAes;
    }

    /**
     * Set amiId
     *
     * @param string $amiId
     *
     * @return Ec2Instance
     */
    public function setAmiId($amiId)
    {
        $this->amiId = $amiId;

        return $this;
    }

    /**
     * Get amiId
     *
     * @return string
     */
    public function getAmiId()
    {
        return $this->amiId;
    }

    /**
     * Set cpuCoreCount
     *
     * @param integer $cpuCoreCount
     *
     * @return Ec2Instance
     */
    public function setCpuCoreCount($cpuCoreCount)
    {
        $this->cpuCoreCount = $cpuCoreCount;

        return $this;
    }

    /**
     * Get cpuCoreCount
     *
     * @return integer
     */
    public function getCpuCoreCount()
    {
        return $this->cpuCoreCount;
    }

    /**
     * Set firstReportAt
     *
     * @param \DateTime $firstReportAt
     *
     * @return Ec2Instance
     */
    public function setFirstReportAt($firstReportAt)
    {
        $this->firstReportAt = $firstReportAt;

        return $this;
    }

    /**
     * Get firstReportAt
     *
     * @return \DateTime
     */
    public function getFirstReportAt()
    {
        return $this->firstReportAt;
    }

    /**
     * Set lastReportAt
     *
     * @param \DateTime $lastReportAt
     *
     * @return Ec2Instance
     */
    public function setLastReportAt($lastReportAt)
    {
        $this->lastReportAt = $lastReportAt;

        return $this;
    }

    /**
     * Get lastReportAt
     *
     * @return \DateTime
     */
    public function getLastReportAt()
    {
        return $this->lastReportAt;
    }

    /**
     * Set uptimeSeconds
     *
     * @param integer $uptimeSeconds
     *
     * @return Ec2Instance
     */
    public function setUptimeSeconds($uptimeSeconds)
    {
        $this->uptimeSeconds = $uptimeSeconds;

        return $this;
    }

    /**
     * Get uptimeSeconds
     *
     * @return integer
     */
    public function getUptimeSeconds()
    {
        return $this->uptimeSeconds;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->hashrateStats = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add hashrateStat
     *
     * @param \AppBundle\Entity\Ec2InstanceHashrateStats $hashrateStat
     *
     * @return Ec2Instance
     */
    public function addHashrateStat(\AppBundle\Entity\Ec2InstanceHashrateStats $hashrateStat)
    {
        $this->hashrateStats[] = $hashrateStat;

        return $this;
    }

    /**
     * Remove hashrateStat
     *
     * @param \AppBundle\Entity\Ec2InstanceHashrateStats $hashrateStat
     */
    public function removeHashrateStat(\AppBundle\Entity\Ec2InstanceHashrateStats $hashrateStat)
    {
        $this->hashrateStats->removeElement($hashrateStat);
    }

    /**
     * Get hashrateStats
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHashrateStats()
    {
        return $this->hashrateStats;
    }
}
