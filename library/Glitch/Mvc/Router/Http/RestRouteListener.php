<?php

namespace Glitch\Mvc\Router\Http;

use Zend\Mvc\MvcEvent;
use Glitch\Mvc\Router\Http\Rest as RestRoute;

class RestRouteListener
{

    public function setupRoutes(MvcEvent $e)
    {
        $options = $e->getApplication()->getServiceManager()->get('Config');
        if (!isset($options['router']['apiRoutes'])) {
            return;
        }

        $sm = $e->getApplication()->getServiceManager();
        $router = $e->getApplication()->getServiceManager()->get('router');

        foreach($options['router']['apiRoutes'] as $key => $routeOptions) {
            $route = RestRoute::factory($routeOptions, $sm);
            $router->addRoute($key, $route);
        }
    }
}