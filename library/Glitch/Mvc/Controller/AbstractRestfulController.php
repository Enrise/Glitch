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
use Zend\View\Model\ViewModel;

use Zend\Debug\Debug;

abstract class AbstractRestfulController extends AbstractActionController
{
    const TYPE_RESOURCE = 'resource';
    const TYPE_COLLECTION = 'collection';

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
    public function passThroughCollection(MvcEvent $e)
    {
        $parts = $e->getRouteMatch()->getUrlParts();
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
    public function passThroughResource(MvcEvent $e)
    {
        $parts = $e->getRouteMatch()->getUrlParts();
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
     * Execute the request. Triggered by the Event Manager.
     *
     * @param  MvcEvent $e
     * @return mixed
     * @throws Exception\DomainException
     */
    public function onDispatch(MvcEvent $e)
    {
        if (!$this->shouldPassThrough($e, 0)) {
            return parent::onDispatch($e);
        }

        $nextPart = $e->getRouteMatch()->getUrlParts()->current();
        return $this->dispatchSubController($e, $nextPart);
    }

    /**
     *
     * Called by a parent class to descend through the controller classes.
     * Either descends further, or dispatches a method of its own.
     *
     * @param MvcEvent $e
     * @throws Exception\DomainException @TODO
     * @return \Pacman\Mvc\Controller\unknown @TODO
     */
    public function doDispatch(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        if (!$routeMatch) {
            throw new Exception\DomainException('Missing route matches; unable to determine action');
        }

        $urlParts = $routeMatch->getUrlParts();
        $urlParts->rewind();

        if (in_array($urlParts->current(), (array) static::$resourceId)) {
            $type = self::TYPE_RESOURCE;
        } elseif (in_array($urlParts->current(), (array) static::$collectionId)) {
            $type = self::TYPE_COLLECTION;
        } else {
            return $this->dispatchMethod($e, 'notFoundAction');
        }

        if ($this->shouldPassThrough($e, $type == self::TYPE_COLLECTION ? 1 : 2)) {
            return $this->passThrough($e, $type);
        }

        return $this->dispatchMethod($e, $this->getRestMethod($type));
    }


    /**
     * Determine if the remaining part of the url contains enough elements
     * to further descend through the tree of controller classes.
     *
     * @param MvcEvent $e
     * @param Integer $countCurController Minimum number of elements required
     * @return boolean
     */
    protected function shouldPassThrough(MvcEvent $e, $countCurController)
    {
        return $e->getRouteMatch()->getUrlParts()->count() > $countCurController;
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
    protected function passThrough(MvcEvent $e, $type)
    {
        if ($type == self::TYPE_COLLECTION) {
            $nextUrlPart =  $e->getRouteMatch()->getUrlParts()->offsetGet(1);
            $this->passThroughCollection($e);
        } else {
            $nextUrlPart =  $e->getRouteMatch()->getUrlParts()->offsetGet(2);
            $this->passThroughResource($e);
        }

        $this->dispatchSubController($e, $nextUrlPart);
    }



    /**
     * Dispatch a child controller.
     *
     * @param MvcEvent $e
     * @param string $nextUrlPart The name of the next url part.
     * @return mixed Usually a ViewModel
     * @throws \exception
     */
    protected function dispatchSubController (MvcEvent $e, $nextUrlPart)
    {
        $className = $this->getSubClass($nextUrlPart);
        if (!$className) {
            // handle error
            throw new \exception('no match');
        }

        $controller = new $className();
        $controller->setEvent($e);
        $controllerLoader = $this->getServiceLocator()->get('ControllerLoader');
        $controllerLoader->injectControllerDependencies($controller, $this->getServiceLocator());

        $controller->setRequest($this->getRequest());
        return $controller->doDispatch($e);
    }


    /**
     * Set the request object.
     * @param  $request @TODO
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * Glob through the controller directory to find any potential children.
     *
     * @param string $nextUrlPart
     * @return string|null Full name of the controller found.
     */
    protected function getSubClass($nextUrlPart)
    {
        $glob = $this->getDir() . '/'
                . substr(get_called_class(), strrpos(get_called_class(), '\\')+1)
                . '/*.php'
           ;

        $controllerLoader = $this->getServiceLocator()->get('ControllerLoader');
        $match = false;
        $iterator = new \GlobIterator($glob);
        foreach ($iterator as $fileinfo) {
            $className = substr($fileinfo->getFilename(), 0, strpos($fileinfo->getFilename(), '.'));
            $className = get_called_class() . '\\' . $className;

            if ($className::isUrlPartMatch($nextUrlPart)) {
                return $className;
            }
        }
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
        $rootController = get_class($e->getTarget());
        $ns = substr($rootController, 0, strrpos($rootController, '\\') + 1);

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
