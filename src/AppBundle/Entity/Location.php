<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Location
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Location
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="address_street", type="string", length=255)
     */
    private $addressStreet;

    /**
     * @var string
     *
     * @ORM\Column(name="address_plz", type="string", length=255)
     */
    private $addressPlz;

    /**
     * @var string
     *
     * @ORM\Column(name="address_location", type="string", length=255)
     */
    private $addressLocation;

    public function __toString()
    {
    	return $this->getName();
    }

    public function getAddress()
    {
    	return $this->getAddressStreet()."\r\n".$this->getAddressPlz().' '.$this->getAddressLocation();
    }
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Location
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Location
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set addressStreet
     *
     * @param string $addressStreet
     *
     * @return Location
     */
    public function setAddressStreet($addressStreet)
    {
        $this->addressStreet = $addressStreet;

        return $this;
    }

    /**
     * Get addressStreet
     *
     * @return string
     */
    public function getAddressStreet()
    {
        return $this->addressStreet;
    }

    /**
     * Set addressPlz
     *
     * @param string $addressPlz
     *
     * @return Location
     */
    public function setAddressPlz($addressPlz)
    {
        $this->addressPlz = $addressPlz;

        return $this;
    }

    /**
     * Get addressPlz
     *
     * @return string
     */
    public function getAddressPlz()
    {
        return $this->addressPlz;
    }

    /**
     * Set addressLocation
     *
     * @param string $addressLocation
     *
     * @return Location
     */
    public function setAddressLocation($addressLocation)
    {
        $this->addressLocation = $addressLocation;

        return $this;
    }

    /**
     * Get addressLocation
     *
     * @return string
     */
    public function getAddressLocation()
    {
        return $this->addressLocation;
    }
}

