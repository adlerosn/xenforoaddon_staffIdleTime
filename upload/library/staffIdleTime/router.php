<?php
class staffIdleTime_router implements XenForo_Route_Interface
{
    public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router)
    {
        $controller = 'staffIdleTime_controller';

        return $router->getRouteMatch($controller, '', '');
    }
}
