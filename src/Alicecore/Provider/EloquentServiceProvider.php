<?php
namespace Alicecore\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Illuminate\Database\Capsule\Manager as Capsule;

class EloquentServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
    public function register(Container $app)
    {
        if(!isset($app['db_options']) || !is_array($app['db_options']) || count($app['db_options']) < 3){
        	throw new \LogicException('Database configuration error!');
        }

        $app['db.default_options'] = array(
            'driver'   => 'pdo_mysql',
            'database' => null,
            'host'     => 'localhost',
            'username' => 'root',
            'password' => null,
            'charset'  => 'utf8',
            'collation'=> 'utf8_unicode_ci',
            'prefix'   => '',
        );

        $app['db.options.initializer'] = $app->protect(function () use ($app) {
            static $initialized = false;
            if ($initialized) {
                return;
            }
            $initialized = true;
            $app['db_options'] = array_replace($app['db.default_options'], $app['db_options']);
        });

        $app['db.start'] = $app->protect(function () use ($app) {
        	$app['db.options.initializer']();
            $capsule = new Capsule();
            $capsule->addConnection($app['db_options']);
            $capsule->setAsGlobal();
            $capsule->bootEloquent();
            $app['db'] = $capsule;
        });
    }

    public function boot(Container $app){
    	$app['db.start']();
    }
}
