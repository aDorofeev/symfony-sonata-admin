<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SpotPriceHistory
 *
 * @ORM\Table(name="spot_price_history")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SpotPriceHistoryRepository")
 */
class SpotPriceHistory
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
     * @ORM\Column(type="string", length=255)
     */
    private $instanceType;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $productDescription;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $spotPrice;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $availabilityZone;


    public function fromAwsResult($result)
    {
        $this->setInstanceType($result['InstanceType']);
        $this->setProductDescription($result['ProductDescription']);
        $this->setSpotPrice($result['SpotPrice']);
        $this->setSpotPrice($result['SpotPrice']);

        /** @var \Aws\Api\DateTimeResult $awsTimestamp */
        $awsTimestamp = $result['Timestamp'];
        $this->setTimestamp($awsTimestamp);

        $this->setAvailabilityZone($result['AvailabilityZone']);
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
     * Set instanceType
     *
     * @param string $instanceType
     *
     * @return SpotPriceHistory
     */
    public function setInstanceType($instanceType)
    {
        $this->instanceType = $instanceType;

        return $this;
    }

    /**
     * Get instanceType
     *
     * @return string
     */
    public function getInstanceType()
    {
        return $this->instanceType;
    }

    /**
     * Set productDescription
     *
     * @param string $productDescription
     *
     * @return SpotPriceHistory
     */
    public function setProductDescription($productDescription)
    {
        $this->productDescription = $productDescription;

        return $this;
    }

    /**
     * Get productDescription
     *
     * @return string
     */
    public function getProductDescription()
    {
        return $this->productDescription;
    }

    /**
     * Set spotPrice
     *
     * @param float $spotPrice
     *
     * @return SpotPriceHistory
     */
    public function setSpotPrice($spotPrice)
    {
        $this->spotPrice = $spotPrice;

        return $this;
    }

    /**
     * Get spotPrice
     *
     * @return float
     */
    public function getSpotPrice()
    {
        return $this->spotPrice;
    }

    /**
     * Set timestamp
     *
     * @param \DateTime $timestamp
     *
     * @return SpotPriceHistory
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Set availabilityZone
     *
     * @param string $availabilityZone
     *
     * @return SpotPriceHistory
     */
    public function setAvailabilityZone($availabilityZone)
    {
        $this->availabilityZone = $availabilityZone;

        return $this;
    }

    /**
     * Get availabilityZone
     *
     * @return string
     */
    public function getAvailabilityZone()
    {
        return $this->availabilityZone;
    }
}
