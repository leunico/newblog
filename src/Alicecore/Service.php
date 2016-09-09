<?php
namespace Alicecore;

use Alicecore\Handle\Extension\AppHandleInterface;

class Service implements AppHandleInterface
{

    public static $app;

	public static $services;

	public static function start(AppFramework $app)
	{
        self::$services = $app->loadconfig('service');
		self::$app = $app;
        self::loagservice();
	}

    public static function loagservice()
    {
        /*foreach(self::$services as $name=>$callback){
            $callback = self::$app['callback_resolver']->resolveCallback($callback);
            //var_dump($name);
            //var_dump($callback);
        }*/
        return true;
    }

}
