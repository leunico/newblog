<?php
namespace Alicecore\Handle\Resolver;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;
use Alicecore\Handle\ControllerHandler;

class ControllerResolver
{
    protected $controllers = array();
    protected $defaultRoute;
    protected $defaultController;
    protected $prefix;
    protected $routesFactory;
    protected $controllersFactory;

    public function __construct(Route $defaultRoute, RouteCollection $routesFactory = null, $controllersFactory = null)
    {
        $this->defaultRoute = $defaultRoute;
        $this->routesFactory = $routesFactory;
        $this->controllersFactory = $controllersFactory;
        $this->defaultController = function (Request $request) {
            throw new \LogicException(sprintf('The "%s" route must have code to run when it matches.', $request->attributes->get('_route'))); //没有设置控制器或者回调函数
        };
    }

    public function mount($prefix, $controllers)
    {
        if (is_callable($controllers)) {
            $collection = $this->controllersFactory ? call_user_func($this->controllersFactory) : new static(new Route(), new RouteCollection());
            call_user_func($controllers, $collection);
            $controllers = $collection;
        } elseif (!$controllers instanceof self) {
            throw new \LogicException('The "mount" method takes either a "ControllerCollection" instance or callable.');
        }

        $controllers->prefix = $prefix;
        $this->controllers[] = $controllers;
    }

    public function match($pattern, $to = null)
    {
        $route = clone $this->defaultRoute;
        $route->setPath($pattern);
        $this->controllers[] = $controller = new ControllerHandler($route);
        if(is_array($to) && array_key_exists('_controller', $to)){
            $route->setDefault('_controller', isset($to['_controller']) ? $to['_controller'] : $this->defaultController);
            unset($to['_controller']);
            foreach ($to as $key => $value) {
                $route->setOption($key, $value);
            }
        }else{
            $route->setDefault('_controller', null === $to ? $this->defaultController : $to);
        }

        return $controller;
    }

    public function get($pattern, $to = null)
    {
        return $this->match($pattern, $to)->method('GET');
    }

    public function post($pattern, $to = null)
    {
        return $this->match($pattern, $to)->method('POST');
    }

    public function put($pattern, $to = null)
    {
        return $this->match($pattern, $to)->method('PUT');
    }

    public function delete($pattern, $to = null)
    {
        return $this->match($pattern, $to)->method('DELETE');
    }

    public function options($pattern, $to = null)
    {
        return $this->match($pattern, $to)->method('OPTIONS');
    }

    public function patch($pattern, $to = null)
    {
        return $this->match($pattern, $to)->method('PATCH');
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->defaultRoute, $method)) {
            throw new \BadMethodCallException(sprintf('Method "%s::%s" does not exist.', get_class($this->defaultRoute), $method));
        }

        call_user_func_array(array($this->defaultRoute, $method), $arguments);

        foreach ($this->controllers as $controller) {
            call_user_func_array(array($controller, $method), $arguments);
        }

        return $this;
    }

    public function flush()
    {
        if (null === $this->routesFactory) {
            $routes = new RouteCollection();
        } else {
            $routes = $this->routesFactory;
        }

        return $this->doFlush('', $routes);
    }

    private function doFlush($prefix, RouteCollection $routes)
    {
        if ($prefix !== '') {
            $prefix = '/'.trim(trim($prefix), '/');
        }

        foreach ($this->controllers as $controller) {
            if ($controller instanceof ControllerHandler) {
                $controller->getRoute()->setPath($prefix.$controller->getRoute()->getPath());
                if (!$name = $controller->getRouteName()) {
                    $name = $base = $controller->generateRouteName('');
                    $i = 0;
                    while ($routes->get($name)) {
                        $name = $base.'_'.++$i;
                    }
                    $controller->bind($name);
                }
                $routes->add($name, $controller->getRoute());
                $controller->freeze();
            } else {
                $controller->doFlush($prefix.$controller->prefix, $routes);
            }
        }

        $this->controllers = array();
        return $routes;
    }
}
