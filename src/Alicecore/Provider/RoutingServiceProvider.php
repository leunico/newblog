<?php
namespace Alicecore\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Alicecore\Handle\Resolver\ControllerResolver;
use Alicecore\Handle\Extension\LazyRequestMatcher;
use Symfony\Component\Routing;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RoutingServiceProvider implements ServiceProviderInterface, EventListenerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $app)
    {
        $app['route_class'] = 'Alicecore\\Handle\\RouteHandler';

        $app['route_factory'] = $app->factory(function ($app) {
            return new $app['route_class']();
        });

        $app['routes_factory'] = $app->factory(function () {
            return new Routing\RouteCollection();
        });

        $app['routes'] = function ($app) {
            return $app['routes_factory'];
        };
        
        $app['url_generator'] = function ($app) {
            return new Routing\Generator\UrlGenerator($app['routes'], $app['request_context']);
        };

        $app['request_context'] = function ($app) {
            $context = new Routing\RequestContext();
            $context->setHttpPort(isset($app['request.http_port']) ? $app['request.http_port'] : 80);
            $context->setHttpsPort(isset($app['request.https_port']) ? $app['request.https_port'] : 443);
            return $context;
        };

        $app['request_matcher'] = function ($app) {
            return new Routing\Matcher\UrlMatcher($app['routes'], $app['request_context']);
        };

        $controllers_factory = function () use ($app, &$controllers_factory) {
            return new ControllerResolver($app['route_factory'], $app['routes_factory'], $controllers_factory);
        };
        $app['controllers_factory'] = $app->factory($controllers_factory);

        $app['controllers'] = function ($app) {
            return $app['controllers_factory'];
        };

        $app['routing.listener'] = function ($app) {
            $urlMatcher = new LazyRequestMatcher(function () use ($app) {
                return $app['request_matcher'];
            });

            return new RouterListener($urlMatcher, $app['request_stack'], $app['request_context'], $app['logger']);
        };
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe(Container $app, EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber($app['routing.listener']);
    }
}
