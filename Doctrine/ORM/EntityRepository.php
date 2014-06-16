<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ResourceBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Tecnocreaciones\Bundle\ResourceBundle\Model\Paginator\Paginator;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository as BaseEntityRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Pagerfanta\Adapter\ArrayAdapter;

/**
 * Doctrine ORM driver entity repository.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class EntityRepository extends BaseEntityRepository implements ContainerAwareInterface
{
    /**
     * @var SecurityContext
     */
    protected $securityContext;
    
    protected $container;
    public function getPaginator(QueryBuilder $queryBuilder)
    {
        $pagerfanta = new Paginator(new DoctrineORMAdapter($queryBuilder));
        $pagerfanta->setContainer($this->container);
        return $pagerfanta;
    }
    
    public function findAllPaginated()
    {
        return $this->getPaginator($this->getQueryBuilder())
        ;
    }
    
    public function setSecurityContext(SecurityContext $securityContext) {
        $this->securityContext = $securityContext;
    }
    
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }
    
    /**
     * Get a user from the Security Context
     *
     * @return mixed
     *
     * @throws \LogicException If SecurityBundle is not available
     *
     * @see Symfony\Component\Security\Core\Authentication\Token\TokenInterface::getUser()
     */
    public function getUser()
    {
        if (!$this->container->has('security.context')) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        if (null === $token = $this->container->get('security.context')->getToken()) {
            return null;
        }

        if (!is_object($user = $token->getUser())) {
            return null;
        }

        return $user;
    }
    
    /**
     * @param mixed $id
     *
     * @return null|object
     */
    public function find($id)
    {
        if(is_array($id) && $id['id']){
            $id = $id['id'];
        }
        return $this
            ->getQueryBuilder()
            ->andWhere($this->getAlias().'.id = '.intval($id))
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
    /**
     * Retorna un paginador con valores escalares (Sin hidratar)
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @return \Tecnocreaciones\Bundle\ResourceBundle\Model\Paginator\Paginator
     */
    public function getScalarPaginator(\Doctrine\ORM\QueryBuilder $queryBuilder)
    {
        $pagerfanta = new \Tecnocreaciones\Bundle\ResourceBundle\Model\Paginator\Paginator(new ArrayAdapter($queryBuilder->getQuery()->getScalarResult()));
        $pagerfanta->setContainer($this->container);
        return $pagerfanta;
    }
}
