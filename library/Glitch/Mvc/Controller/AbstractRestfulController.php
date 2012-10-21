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
     * @var string|array
     */
    protected static $resourceId;

    /**
     * @var string|array
     */
    protected static $collectionId;

    /**
     * Should be abstract
     */
    abstract protected function getDir();

    /**
     * You will want to override this. Usually calling the parent might come in handy.
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

        return array('key' => $key, 'value' => $value);
    }

    /**
     *
     * @param unknown_type $part
     * @return boolean
     */
    public static function isUrlPartMatch($part) {

        return in_array($part, (array) static::$collectionId) ||
        in_array($part, (array) static::$resourceId);
    }


    /**
     * Execute the request
     *
     * @param  MvcEvent $e
     * @return mixed
     * @throws Exception\DomainException
     */
    public function onDispatch(MvcEvent $e)
    {
        $this->doDispatch($e);
    }

    /**
     *
     * @param MvcEvent $e
     * @throws Exception\DomainException
     * @return \Pacman\Mvc\Controller\unknown
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

        if ($urlParts->count() > ($type == self::TYPE_COLLECTION ? 1 : 2)) {
            return $this->passThrough($e, $type);
        }

        $this->dispatchMethod($e, $this->getRestMethod($type));
    }

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
     *
     * @param MvcEvent $e
     * @param unknown_type $type
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

        $className = $this->getSubClass($nextUrlPart);
        if (!$className) {
            // handle error
            throw new \exception('no match');
        }

        $controller = new $className();
        $controller->setEvent($e);
        $controllerLoader = $this->getServiceLocator()->get('ControllerLoader');
        $controllerLoader->injectControllerDependencies($controller, $this->getServiceLocator());

        $controller->onDispatch($e);
    }

    /**
     *
     * @param string $nextUrlPart
     * @return string
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
     * What does this return?
     * @param string $method
     * @return unknown
     */
    protected function dispatchMethod(MvcEvent $e, $method)
    {
        $actionResponse = $this->$method();
        $e->setResult($actionResponse);
        return $actionResponse;
    }

}
