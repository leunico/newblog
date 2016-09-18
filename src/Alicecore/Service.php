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
        self::loadservice();
    }

    public static function loadservice()
    {
        foreach(self::$services as $name=>$className){

            if ($className instanceof Closure) {
                self::$app[$name] = $className();
            }

            if(!is_string($className))
                continue;

            #反射类加载自定义服务~
            $reflector = new \ReflectionClass($className);
            if (!$reflector->isInstantiable()) {
                throw new Exception('Error:Can\'t instantiate this.');
            }

            $interface = $reflector->getInterfaces();

            if (!array_key_exists("Alicecore\Handle\Extension\ServiceInterface", $interface)
                && !array_key_exists("Alicecore\Handle\Extension\MiddlewareInterface", $interface)) {
                throw new Exception('Error:Service does not implement interface');
            }

            $constructor = $reflector->getConstructor();
            if (is_null($constructor)) {
                self::$app[$name] = new $className();
            }else{
                self::$app[$name] = $reflector->newInstanceArgs([self::$app]);
            }
        }

        return true;
    }

}
