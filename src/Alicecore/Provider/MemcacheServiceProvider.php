<?php
namespace Alicecore\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\MemcacheSessionHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Alicecore\EventListener\MemcacheListener;
use Alicecore\Handle\Extension\MemcacheExension;


class MemcacheServiceProvider implements ServiceProviderInterface, EventListenerProviderInterface
{
    public function register(Container $app)
    {
        if(!isset($app['memcache_options']) || !is_array($app['memcache_options'])){
        	throw new \LogicException('Memcacahe configuration error!');
        }

        $app['memcache.default_options'] = array(
            'host'       => 'localhost',
            'port'       => 11211,
            'expiretime' => 3600,
            'prefix'     => '',
        );

        $app['memcache.options.initializer'] = $app->protect(function () use ($app) {
            static $initialized = false;
            if ($initialized) {
                return;
            }

            $initialized = true;
            $app['memcache_options'] = array_replace($app['memcache.default_options'], $app['memcache_options']);
        });

        $app['memcache.start'] = $app->protect(function () use ($app) {
            if(!isset($app['memcache_switch']) || $app['memcache_switch'] === FALSE){
                $app['memcache'] = function () {
                    # throw new \LogicException('The Memcache is disabled');
                    return new MemcacheExension();
                };
                return false;
            }
        	$app['memcache.options.initializer']();
            $config = $app['memcache_options'];
            extract($config, EXTR_SKIP);

            try {
                $memcache = new \Memcache();
                $memcache->connect($host, $port);
            } catch (\LogicException $e) {
                throw new \LogicException('Memcache link error:'.$e);
            }
            $app['memcache'] = new MemcacheSessionHandler($memcache, array('expiretime' => $expiretime-time(), 'prefix' => $prefix));
        });
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe(Container $app, EventDispatcherInterface $dispatcher)
    {
        if(!$app['memcache.start']()){
            # $dispatcher->addSubscriber(new MemcacheListener($app));
        }
    }
}
