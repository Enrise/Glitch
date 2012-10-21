<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace Glitch\Mvc\Router\Http\Rest;

use Zend\Mvc\Router\Http\RouteMatch as HttpRouteMatch;
use Glitch\Mvc\Router\Http\Rest as HttpRoute;
use Zend\Http\Request;
use Zend\StdLib\SplStack;
use SplQueue;

/**
 * RouteInterface match.
 *
 * @package    Zend_Mvc_Router
 */
class RouteMatch extends HttpRouteMatch
{
    const PATH_SEPARATOR = '/';

    /**
     *
     * @var \SplQueue
     */
    protected $urlParts;

    /**
     * Create a part RouteMatch with given parameters and length.
     *
     * @param  Request $request
     * @param  array   $params
     * @param  integer $length
     */
    public function __construct(Request $request, HttpRoute $route, HttpRouteMatch $match)
    {
        parent::__construct(array('controller' => $match->getParam('controller')));

        $this->length = $match->getLength();
        $path = substr(
                    $request->getUri()->getPath(),
                    - ($match->getLength() - strlen($route->getRoutePath()))
        );

        $parts = explode(self::PATH_SEPARATOR, $path);
        if (!reset($parts)) { // First element is empty
            array_shift($parts);
        }

        $stack = new SplQueue();
        array_map(function($val) use ($stack) { $stack[] = $val; }, $parts);
        $stack->rewind();
        $this->urlParts = $stack;
    }

    /**
     *
     * @return \Zend\StdLib\SplQueue
     */
    public function getUrlParts()
    {
        return $this->urlParts;
    }

}
