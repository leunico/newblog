<?php
namespace Alicecore\Handle\Resolver;

use Pimple\Container;

class CallbackResolver
{
    const SERVICE_PATTERN = "/[A-Za-z0-9\._\-]+::[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/";

    private $app;

    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    public function isValid($name)
    {
        return is_string($name) && (preg_match(static::SERVICE_PATTERN, $name) || isset($this->app[$name]));
    }

    public function convertCallback($name)
    {
        if (preg_match(static::SERVICE_PATTERN, $name)) {
            list($service, $method) = explode('::', $name, 2);
            $callback = array($this->app[$service], $method); #某个服务下面的一个方法
        } else {
            $service = $name;
            $callback = $this->app[$name]; #某个服务
        }

        if (!is_callable($callback)) {  #检测参数是否为合法的可调用结构
            throw new \InvalidArgumentException(sprintf('Service "%s" is not callable.', $service));
        }

        return $callback;
    }

    public function resolveCallback($name)
    {
        return $this->isValid($name) ? $this->convertCallback($name) : $name;
    }
}
