<?php

// namespace Pacman\Mvc\Router\Http;
namespace Glitch\Mvc\Router\Http;

use Glitch\Stdlib\SubstringSearcher;
use Zend\Mvc\Router\Http\Part;
use Glitch\Mvc\Router\Http\Rest\RouteMatch as RestRouteMatch;
use Zend\Mvc\Router\Http\RouteMatch as HttpRouteMatch;
use Zend\Mvc\Router\RoutePluginManager;
use \Zend\ServiceManager\ServiceManager;
use Traversable;
use Zend\Mvc\Router;
use Zend\Mvc\Router\Exception;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\RequestInterface as Request;
use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


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

    protected $sm;

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
    public static function factory($customOptions = array(), ServiceManager $sm = null)
    {
        if (!$sm) {
            throw new \Exception('An instance of the service manager is required but none was supplied');
        }
        $options = self::$defaultRoutes;
        $options['route']['options']['route'] = $customOptions['route'];
        $options['route']['options']['defaults']['controller'] =
        $customOptions['defaults']['controller'];

        $plugins = array(
                'Literal' => 'Zend\Mvc\Router\Http\Literal',
                'wildcard' => 'Zend\Mvc\Router\Http\Wildcard',
        );
        $options['route_plugins'] = new \Zend\Mvc\Router\RoutePluginManager();
        foreach ($plugins as $name => $class) {
            $options['route_plugins']->setInvokableClass($name, $class);
        }

        $instance = parent::factory($options);
        $instance->setServiceManager($sm);
        return $instance;
    }

    public function setServiceManager(ServiceManager $sm)
    {
        $this->sm = $sm;
    }



    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        var_dump($instance === $this); exit;
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

        $restRouteMatch->setMatchedRouteName(__CLASS__);
        $controller = $this->passThrough($restRouteMatch->getParam('controller'), $restRouteMatch);

        $restRouteMatch->setParam('controller', $controller);


        return $restRouteMatch;
    }

    protected function passThrough($controllerName, $routeMatch)
    {
        $routeMatch->getUrlParts()->rewind();
        $controllerLoader = $this->sm->get('controllerloader');
        $controller = $controllerLoader->get($controllerName);
        if (!$controller->shouldPassThrough($routeMatch)) {
            return $controllerName;
        }

        $controller->passThrough($routeMatch);

        $nextUrlPart = $routeMatch->getUrlParts()->offsetGet(0);
        $controllers = $controllerLoader->getServiceLocator()->getCanonicalNames();
        $subControllers = SubstringSearcher::searchArray($controllers, $controllerName);
        foreach($subControllers as $controller) {
            if ($controller::isUrlPartMatch($nextUrlPart)) {
                return $this->passThrough($controller, $routeMatch);
            }
        }

        throw new \Exception('No match was found for this url? This couldn\'t happen.');
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