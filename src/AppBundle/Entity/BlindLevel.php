<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BlindLevel
 *
 * @ORM\Table(name="blind_level")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BlindLevelRepository")
 */
class BlindLevel
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
     * @var string
     *
     * @ORM\Column(name="level", type="integer", nullable=false)
     */
    private $level;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", nullable=false, length=255)
     */
    private $name;
    
    /**
     * @var int
     *
     * @ORM\Column(name="duration", type="integer", nullable=true)
     */
    private $duration;

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
     * Set name
     *
     * @param string $name
     *
     * @return BlindLevel
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
     * Set level
     *
     * @param string $level
     *
     * @return BlindLevel
     */
    public function setLevel($level)
    {
        $this->level = $level;
        
        return $this;
    }
    
    /**
     * Get level
     *
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }
    
    /**
     * Set duration
     *
     * @param integer $duration
     *
     * @return BlindLevel
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }
}

