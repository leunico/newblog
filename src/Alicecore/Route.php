<?php
namespace Alicecore;

use Alicecore\Handle\Extension\AppHandleInterface;

class Route implements AppHandleInterface
{

    public static $app;

    public static function start(AppFramework $app)
    {
        $app['controllers']->get('/hello/{name}', function ($name) use ($app) {
            return 'Hello '.$app->escape($name).' This is AliceFramework!!!';
        });

        self::$app = $app;
        require_once __DIR__.'/../route.php';
    }

    public static function get($pattern, $to = null)
    {
        return self::$app['controllers']->get($pattern, $to);
    }

    public static function match($pattern, $to = null)
    {
        return self::$app['controllers']->match($pattern, $to);
    }

    public static function post($pattern, $to = null)
    {
        return self::$app['controllers']->post($pattern, $to);
    }

    public static function put($pattern, $to = null)
    {
        return self::$app['controllers']->put($pattern, $to);
    }

    public static function delete($pattern, $to = null)
    {
        return self::$app['controllers']->delete($pattern, $to);
    }

    public static function options($pattern, $to = null)
    {
        return self::$app['controllers']->options($pattern, $to);
    }

    public static function patch($pattern, $to = null)
    {
        return self::$app['controllers']->patch($pattern, $to);
    }

}
