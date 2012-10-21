<?php

// namespace Pacman\Mvc\Router\Http;
namespace Glitch\Mvc\Router\Http;

use Zend\Mvc\Router\Http\Part;
use Glitch\Mvc\Router\Http\Rest\RouteMatch as RestRouteMatch;
use Zend\Mvc\Router\Http\RouteMatch as HttpRouteMatch;
use Zend\Mvc\Router\RoutePluginManager;
use Traversable;
use Zend\Mvc\Router;
use Zend\Mvc\Router\Exception;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\RequestInterface as Request;


use Zend\Debug\Debug;
/**
 * Wildcard route.
 *
 * @package    Zend_Mvc_Router
 * @subpackage Http
 */
class Rest extends Part
    implements Router\RouteInterface,
               Router\Http\RouteInterface
{

    /**
     *
     * @var string
     */
    protected $routePath;

    /**
     * Private on purpose. API should not be considered stable yet
     * @var array
     */
    private static $defaultRoutes = array(
             'route' => array(
                 'type' => 'Literal',
                 'options' => array(
                     'defaults' => array('action' => 'index')
                 )
             ),
             'may_terminate' => true,
             'child_routes' => array(
                     array(
                             'type'    => 'wildcard',
                             'options' => array(
                                     'route'    => '*',
                             ),
                     ),
/*                      array(
                             'type'    => 'Literal',
                             'options' => array(
                                     'route'    => '/',
                             ),
                     ), */
                 ),
            );

    /**
     * factory(): defined by RouteInterface interface.
     *
     * The options array is not merged on purpose. Want to maintain the
     * possibility to alter behavior later.
     *
     * @see    Route::factory()
     * @param  mixed $options
     * @throws Exception\InvalidArgumentException
     * @return Part
     */
    public static function factory($customOptions = array())
    {
        $options = self::$defaultRoutes;
        $options['route']['options']['route'] = $customOptions['route'];
        $options['route']['options']['defaults']['controller'] = $customOptions['defaults']['controller'];

        $plugins = array(
                'Literal' => 'Zend\Mvc\Router\Http\Literal',
                'wildcard' => 'Zend\Mvc\Router\Http\Wildcard',
        );
        $options['route_plugins'] = new \Zend\Mvc\Router\RoutePluginManager();
        foreach ($plugins as $name => $class) {
            $options['route_plugins']->setInvokableClass($name, $class);
        }

        return parent::factory($options);
    }

    /**
     * Create a new part route.
     *
     * @param  mixed              $route
     * @param  boolean            $mayTerminate
     * @param  RoutePluginManager $routePlugins
     * @param  array|null         $childRoutes
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($route, $mayTerminate, RoutePluginManager $routePlugins, array $childRoutes = null)
    {
        parent::__construct($route, $mayTerminate, $routePlugins, $childRoutes);

        $this->routePath = $route['options']['route'];
    }

    /**
     * match(): defined by RouteInterface interface.
     *
     * @see    Route::match()
     * @param  Request  $request
     * @param  int|null $pathOffset
     * @return RouteMatch|null
     */
    public function match(Request $request, $pathOffset = null)
    {
        if ($pathOffset) {
            throw new \Exception('
                Since $pathOffset has not been documented, this parameter wasn\'t implemented'
            );
        }

        if (!($match = parent::match($request, $pathOffset)) instanceof HttpRouteMatch) {
            return;
        }

        $restRouteMatch = new RestRouteMatch(
                $request,
                $this,
                $match
        );

        return $restRouteMatch;
    }

    /**
     *
     * @return string
     */
    public function getRoutePath()
    {
        return $this->routePath;
    }

    /**
     * Assemble the route.
     *
     * @param  array $params
     * @param  array $options
     * @return mixed
    */
    public function assemble(array $params = array(), array $options = array())
    {
        throw new \Exception(
                'The Assemble() method is not implemented in the REST router'
        );
    }

    public function getAssembledParams()
    {
        throw new \exception('Not Implemented');
    }

}