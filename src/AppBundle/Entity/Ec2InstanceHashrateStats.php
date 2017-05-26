<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ec2InstanceHashrateStats
 *
 * @ORM\Table(name="ec2_instance_hashrate_stats")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Ec2InstanceHashrateStatsRepository")
 */
class Ec2InstanceHashrateStats
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
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
     * @ORM\Column(name="coinCode", type="string", length=63)
     */
    private $coinCode;

    /**
     * @var int
     *
     * @ORM\Column(name="recordCount", type="integer")
     */
    private $recordCount;

    /**
     * @var float
     *
     * @ORM\Column(name="hashrateSum", type="float")
     */
    private $hashrateSum;

    /**
     * @var float
     *
     * @ORM\Column(name="hashrateAvg", type="float")
     */
    private $hashrateAvg;


    public function getView()
    {
        return sprintf('%s: %s',
            $this->getCoinCode(),
            number_format($this->getHashrateAvg(), 3)
        );
    }


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
     * @return Ec2InstanceHashrateStats
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
     * Set recordCount
     *
     * @param integer $recordCount
     *
     * @return Ec2InstanceHashrateStats
     */
    public function setRecordCount($recordCount)
    {
        $this->recordCount = $recordCount;

        return $this;
    }

    /**
     * Get recordCount
     *
     * @return int
     */
    public function getRecordCount()
    {
        return $this->recordCount;
    }

    /**
     * Set hashrateSum
     *
     * @param float $hashrateSum
     *
     * @return Ec2InstanceHashrateStats
     */
    public function setHashrateSum($hashrateSum)
    {
        $this->hashrateSum = $hashrateSum;

        return $this;
    }

    /**
     * Get hashrateSum
     *
     * @return float
     */
    public function getHashrateSum()
    {
        return $this->hashrateSum;
    }

    /**
     * Set hashrateAvg
     *
     * @param float $hashrateAvg
     *
     * @return Ec2InstanceHashrateStats
     */
    public function setHashrateAvg($hashrateAvg)
    {
        $this->hashrateAvg = $hashrateAvg;

        return $this;
    }

    /**
     * Get hashrateAvg
     *
     * @return float
     */
    public function getHashrateAvg()
    {
        return $this->hashrateAvg;
    }

    /**
     * Set ec2Instance
     *
     * @param \AppBundle\Entity\Ec2Instance $ec2Instance
     *
     * @return Ec2InstanceHashrateStats
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
