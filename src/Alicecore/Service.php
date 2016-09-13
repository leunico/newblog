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
        foreach(self::$services as $name=>$className){

            if(!is_string($className))
                continue;

            if ($className instanceof Closure) {
                self::$app[$name] = $className(self::$app);
            }

            $reflector = new \ReflectionClass($className);
            if (!$reflector->isInstantiable()) {
                throw new Exception('Error:Can\'t instantiate this.');
            }

            $constructor = $reflector->getConstructor();
            if (is_null($constructor)) {
                self::$app[$name] = new $className();
            }

            self::$app[$name] = $reflector->newInstanceArgs([self::$app]);
        }
        return true;
    }

}
