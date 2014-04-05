<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com.ve
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ResourceBundle\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Description of ManagerEntityRepository
 *
 * @author Anais Ortega <adcom23@tecnocreaciones.com.ve>
 */
class ManagerEntityRepository implements ContainerAwareInterface
{
    /**
     *
     * @var Registry
     */
    private $doctrine;
    
    public function setDoctrine(Registry $doctrine) {
        $this->doctrine = $doctrine;
    }
    
    function getRepository($persistentObjectName) {
        $repository = $this->doctrine->getRepository($persistentObjectName);
        if(method_exists($repository, 'setSecurityContext')){
            $repository->setSecurityContext($this->securityContext);
        }
        if($repository instanceof ContainerAwareInterface){
            $repository->setContainer($this->container);
        }
        return $repository;
    }

    public function setSecurityContext(SecurityContext $securityContext) {
        $this->securityContext = $securityContext;
    }
    
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

}
