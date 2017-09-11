<?php
namespace AppBundle\Model;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

abstract class AbstractManager
{
    protected $_em;
    
    protected $_repo;
    
    public function __construct(RegistryInterface $doctrine, EntityRepository $repo)
    {
        $this->_em = $doctrine->getManager();
        $this->_repo = $repo;
    }
    
    abstract public function create();
    
    abstract public function find($id);
    
    abstract public function findAll();
       
    /**
     * 
     * @return \Doctrine\ORM\EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return $this->_em;
    }
    
    /**
     * 
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getRepository()
    {
        return $this->_repo;
    }
}