<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com.ve
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ResourceBundle\Model\Paginator;

use Pagerfanta\Pagerfanta as BasePagerfanta;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Router;

/**
 * Pagerfanta modificado para serializarlo
 *
 * @author Anais Ortega <adcom23@tecnocreaciones.com.ve>
 */
class Paginator extends BasePagerfanta implements ContainerAwareInterface
{
    protected $route = null;
    protected $container;
    
    /**
     * Devuelve un formato estandar de trabajo
     */
    const FORMAT_ARRAY_DEFAULT = 'default';
    
    /**
     * Devuelve un formato para que pueda ser leido por el plugin DataTables de jQuery
     */
    const FORMAT_ARRAY_DATA_TABLES = 'dataTables';
    
    private $formatArray = array(
        self::FORMAT_ARRAY_DEFAULT,self::FORMAT_ARRAY_DATA_TABLES
    );
            
    function formatToArrayDefault($route = null,array $parameters = array()) {
        $links = array(
            'self'  => array('href' => ''),
            'first' => array('href' => ''),
            'last'  => array('href' => ''),
            'next'  => array('href' => ''),
            'previous'  => array('href' => ''),
        );
        $paginator = array(
                        'getNbResults' => $this->getNbResults(),
                        'getCurrentPage' => $this->getCurrentPage(),
                        'getNbPages' => $this->getNbPages(),
                        'getMaxPerPage' => $this->getMaxPerPage(),
                    );
        if($route != null){
            $links['first']['href'] = $this->generateUrl($route, array_merge($parameters, array('page' => 1)));
            $links['self']['href'] = $this->generateUrl($route, array_merge($parameters, array('page' => $this->getCurrentPage())));
            $links['last']['href'] = $this->generateUrl($route, array_merge($parameters, array('page' => $this->getNbPages())));
            if($this->hasPreviousPage()){
                $links['previous']['href'] = $this->generateUrl($route, array_merge($parameters, array('page' => $this->getPreviousPage())));
            }
            if($this->hasNextPage()){
                $links['next']['href'] = $this->generateUrl($route, array_merge($parameters, array('page' => $this->getNextPage())));
            }
        }
        $results = $this->getCurrentPageResults()->getArrayCopy();
        return array(
            '_links' => $links,
            '_embedded' => array(
                'results' => $results,
                'paginator' => $paginator
            ),
        );
    }
    
    function formatToArrayDataTables($route = null,array $parameters = array()) {
        $results = $this->getCurrentPageResults()->getArrayCopy();
        $data = array(
            'sEcho' => $this->getCurrentPage(),
            'iTotalRecords' => $this->getNbResults(),
            'iTotalDisplayRecords' => $this->getNbResults(),
            'aaData' => $results,
        );
        return $data;
    }
    
    function toArray($route = null,array $parameters = array(),$format = self::FORMAT_ARRAY_DEFAULT) {
        if(in_array($format, $this->formatArray)){
            $method = 'formatToArray'.ucfirst($format);
            return $this->$method($route,$parameters);
        }
    }
    
    protected function  generateUrl($route,array $parameters){
        return $this->container->get('router')->generate($route, $parameters, Router::ABSOLUTE_URL);
    }
    
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }
}
