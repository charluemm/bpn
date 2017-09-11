<?php
namespace AppBundle\Entity;
use AppBundle\Model\AbstractManager;

class PlayerManager extends AbstractManager
{

    /**
     *
     * {@inheritdoc}
     *
     * @see \AppBundle\Model\AbstractManager::create()
     */
    public function create ()
    {
        return new Player();
    }

    
    public function findAll()
    {
        return $this->getRepository()->findAll();
    }
    
    public function find($id)
    {
        return $this->getRepository()->find($id);
    }
}