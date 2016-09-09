<?php
namespace Alicecore\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Alicecore\Handle\Resolver\CallbackResolver;
use Alicecore\EventListener\ConverterListener;
use Alicecore\EventListener\MiddlewareListener;
use Alicecore\EventListener\StringToResponseListener;
use Alicecore\Handle\Extension\ControllerResolver;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadataFactory;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\EventListener\ResponseListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpCache\HttpCache;
use Symfony\Component\HttpKernel\HttpCache\Store;
#use Symfony\Component\HttpKernel\Controller\ControllerResolver;

class HttpKernelServiceProvider implements ServiceProviderInterface, EventListenerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $app)
    {
        $app['resolver'] = function ($app) {
            return new ControllerResolver($app);
        };

        $app['argument_metadata_factory'] = function ($app) {
            return new ArgumentMetadataFactory();
        };

        $app['kernel'] = function ($app) {
            if(isset($app['httpcache_switch']) && $app['httpcache_switch'] === TRUE){
                return new HttpCache(new HttpKernel($app['dispatcher'], $app['resolver'], $app['request_stack']), new Store(__DIR__.'/../../cache'));
            }
            return new HttpKernel($app['dispatcher'], $app['resolver'], $app['request_stack']);
        };

        $app['request_stack'] = function () {
            return new RequestStack();
        };
//>>
        /*$app['request'] = function () {
            return new Request();
        };*/
//<<
        $app['dispatcher'] = function () {
            return new EventDispatcher();
        };

        $app['session'] = function () {
            return new Session();
        };

        $app['callback_resolver'] = function ($app) {
            return new CallbackResolver($app);
        };
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe(Container $app, EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber(new ResponseListener($app['charset']));
        $dispatcher->addSubscriber(new MiddlewareListener($app));
        $dispatcher->addSubscriber(new ConverterListener($app['routes'], $app['callback_resolver']));
        $dispatcher->addSubscriber(new StringToResponseListener());
    }
}
