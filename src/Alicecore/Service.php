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
        foreach(self::$services as $name=>$callback){

            if(!is_array($callback)){
                $className = $callback;
            }else{
                $className = isset($callback['class']) ? $callback['class'] : $callback[0];
                $instances = isset($callback['argument']) ? $callback['argument'] : '';
                if($instances == 'app')
                    $instances = [self::$app];
                if(is_array($instances)){
                    $service = [];
                    foreach ($instances as $name) {
                        $service[] = self::$app[$name];
                    }
                    $instances = $service;    
                }
            }

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

            if ($instances) {
                 self::$app[$name] = $reflector->newInstanceArgs($instances);
            } else {
                throw new \InvalidArgumentException("Missing parameters");
            }
        }
        return true;
    }

}
