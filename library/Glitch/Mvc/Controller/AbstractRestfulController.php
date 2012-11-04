<?php
/**
 * Zend Framework (http://framework.zend.com/)
*
* @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
* @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
* @license   http://framework.zend.com/license/new-bsd New BSD License
*/

namespace Glitch\Mvc\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use \Glitch\Mvc\Router\Http\Rest\RouteMatch;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;

use Zend\Debug\Debug;

abstract class AbstractRestfulController extends AbstractActionController
{
    const TYPE_RESOURCE = 'resource';
    const TYPE_COLLECTION = 'collection';
    const TYPE_ROOT = 'root';

    const MAGIC_SEPARATOR = '\Controller\\';

    /**
     * This string|array is used to determine if the next part of the string
     * matches this class and if it denotes a resource.
     *
     * @var string|array
     */
    protected static $resourceId;

    /**
     * This string|array is used to determine if the next part of the string
     * matches this class and if it denotes a collection.
     * @var string|array
     */
    protected static $collectionId;

    /**
     * Used to glob the controller directory structure.
     *
     * Unfortunately the Service Locator does not have a wildcard search. Yet.
     */
    abstract protected function getDir();

    /**
     * When a subclass its target is called upon this method is called.
     * Usually it is used to fill the request object with objects based on the url parameters.
     *
     * You will want to override this. Usually, calling the parent might come in handy.
     *
     * @param MvcEvent $e
     * @return multitype:unknown
     */
    public function passThroughCollection(RouteMatch $routeMatch)
    {
        $parts = $routeMatch->getUrlParts();
        $key = $parts->shift();

        return array('key' => $key);
    }

    /**
     * When a subclass is called upon this method is calle if a resource was given.
     * Usually it is used to fill the request object with objects based on the url parameters.
     *
     * You will want to override this. Usually calling the parent might come in handy.
     *
     * @param MvcEvent $e
     * @return array for convenience when overriding.
     */
    public function passThroughResource(RouteMatch $routeMatch)
    {
        $parts = $routeMatch->getUrlParts();
        $key = $parts->shift();
        $value = $parts->shift();
        $this->getRequest()->setMetadata($key, $value);

        return array($key, $value);
    }

    /**
     * Return true if a keyword (from the url) applies to this class.
     *
     * @param string $part
     * @return boolean
     */
    public static function isUrlPartMatch($part) {

        return in_array($part, (array) static::$collectionId) ||
               in_array($part, (array) static::$resourceId);
    }


    /**
     * Determine if the remaining part of the url contains enough elements
     * to further descend through the tree of controller classes.
     *
     * @param MvcEvent $e
     * @param Integer $countCurController Minimum number of elements required
     * @return boolean
     */
    public function shouldPassThrough(RouteMatch $routeMatch)
    {
        $urlParts = $routeMatch->getUrlParts();

        if (!static::$collectionId && !static::$resourceId) {  // root
            return $urlParts->count() > 0;
        }

        if (in_array($urlParts->current(), (array) static::$resourceId)) {
            return $urlParts->count() > 2;
        }

        if (in_array($urlParts->current(), (array) static::$collectionId)) {
            return $urlParts->count() > 1;
        }

        // If you got here, then what?!
        throw new \Exception('This scenario had not been anticipated.');
    }


    /**
     * Determine the name of the method to call based on the HTTP Request Method used,
     * and if a collection or resource was requested.
     *
     * @param string $type Either TYPE_COLLECTION or TYPE_RESOURCE
     * @return string The name of the method.
     */
    protected function getRestMethod($type)
    {
        return $this->getMethodFromAction(
                    $type . '.' . strtolower($this->getRequest()->getMethod())
                );
    }

    /**
     *    The method specified in the Request-Line is not allowed for the
     *    resource identified by the Request-URI. The response MUST include an
     *    Allow header containing a list of valid methods for the requested
     *    resource.
     *
     * @see \Zend\Mvc\Controller\AbstractController::__call()
     */
    public function __call($method, $params)
    {
        if (substr($method, -6) != 'Action') {
            return parent::__call($method, $params);
        }

        throw new \exception(501);
    }


    /**
     * When a subcontroller is to be called, call one of the passthrough methods first.
     *
     * @param MvcEvent $e
     * @param string $type either self::TYPE_COLLECTION or self::TYPE_RESOURCE
     * @throws \exception
     */
    public function passThrough($routeMatch)
    {
        $urlParts = $routeMatch->getUrlParts();

        if (!static::$collectionId && !static::$resourceId) {  // root
            return;
        }

        if (in_array($urlParts->current(), (array) static::$collectionId)) {
            return $this->passThroughCollection($routeMatch);
        }

        if (in_array($urlParts->current(), (array) static::$resourceId)) {
            return $this->passThroughResource($routeMatch);
        }

        throw new \Exception('now what?');
    }

    public function onDispatch(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        if (!$routeMatch) {
            throw new \Exception('Missing route matches; unable to determine action');
        }

        $urlParts = $routeMatch->getUrlParts();
        $urlParts->rewind();

        if (in_array($urlParts->current(), (array) static::$resourceId)) {
            $type = self::TYPE_RESOURCE;
        } elseif (in_array($urlParts->current(), (array) static::$collectionId)) {
            $type = self::TYPE_COLLECTION;
        } elseif (!static::$resourceId && !static::$collectionId) {
            $type = self::TYPE_ROOT;
        }  else {
            return $this->dispatchMethod($e, 'notFoundAction');
        }

        $actionResponse = $this->dispatchMethod($e, $this->getRestMethod($type));
        $e->setResult($actionResponse);
        return $actionResponse;
    }


    /**
     * Dispatch the given action method
     *
     * @param string $method
     * @return mixed But usally a ViewModel
     */
    protected function dispatchMethod(MvcEvent $e, $method)
    {
        $actionResponse = $this->$method();
        $this->prepForTemplateListener($e, $method);

        return $actionResponse;
    }

    /**
     * Return the id of a resource (from the url)
     * @return NULL|string
     */
    protected function getResourceId()
    {
        $e = $this->getEvent();
        if (!$e->getRouteMatch()->getUrlParts()->offsetExists(1)) {
            return null;
        }

        return $e->getRouteMatch()->getUrlParts()->offsetGet(1);
    }

    /**
     * We need to do a little hacking here, because
     * InjectTemplateListener::deriveControllerClass() assumes a flat structure
     *
     * @param MvcEvent $e
     * @param unknown_type $method
     * @return null
     *
     * @see https://twitter.com/Freeaqingme/status/263779825469239297
     */
    protected function prepForTemplateListener(MvcEvent $e, $method)
    {
        $basePos = strlen(self::MAGIC_SEPARATOR) + strpos(get_called_class(), self::MAGIC_SEPARATOR);
        $rootController = substr(get_called_class(), 0, strpos(get_called_class(), '\\', $basePos)-1);

        $ns = substr($rootController, 0, strrpos($rootController, '\\') );

        $controller = get_called_class();
        $fakeTarget = substr($controller, 0, strpos($controller, '\\'))
                    . '\\' . str_replace('\\', '/', substr($controller, strlen($ns)));
        $e->setTarget($fakeTarget);

        if (substr($method, -6) == 'Action') {
            $method = substr($method, 0, -6);
        }

        $e->getRouteMatch()->setParam('action', $method);
    }

}
