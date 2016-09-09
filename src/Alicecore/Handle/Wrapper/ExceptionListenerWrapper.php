<?php
namespace Alicecore\Wrapper;

use Alicecore\AppFramework;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class ExceptionListenerWrapper
{
    protected $app;
    protected $callback;

    /**
     * Constructor.
     *
     * @param AppFramework  $app      An AppFramework instance
     * @param callable      $callback
     */
    public function __construct(AppFramework $app, $callback)
    {
        $this->app = $app;
        $this->callback = $callback;
    }

    public function __invoke(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $this->callback = $this->app['callback_resolver']->resolveCallback($this->callback);

        if (!$this->shouldRun($exception)) {
            return;
        }

        $code = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;

        $response = call_user_func($this->callback, $exception, $event->getRequest(), $code); #你的监听方法处理的回调

        $this->ensureResponse($response, $event);
    }

    protected function shouldRun(\Exception $exception) #解析方法是否存在
    {
        if (is_array($this->callback)) {
            $callbackReflection = new \ReflectionMethod($this->callback[0], $this->callback[1]);
        } elseif (is_object($this->callback) && !$this->callback instanceof \Closure) {
            $callbackReflection = new \ReflectionObject($this->callback);
            $callbackReflection = $callbackReflection->getMethod('__invoke');
        } else {
            $callbackReflection = new \ReflectionFunction($this->callback);
        }

        if ($callbackReflection->getNumberOfParameters() > 0) {
            $parameters = $callbackReflection->getParameters();
            $expectedException = $parameters[0];
            if ($expectedException->getClass() && !$expectedException->getClass()->isInstance($exception)) {
                return false;
            }
        }

        return true;
    }

    protected function ensureResponse($response, GetResponseForExceptionEvent $event)
    {
        if ($response instanceof Response) {
            $event->setResponse($response);
        } else {
            #如果你的监听返回的不是一个Response对象，这里会重新注册KernelEvents::VIEW事件(执行StringToResponse订阅者的监听)，确保返回的是Response对象！
            $viewEvent = new GetResponseForControllerResultEvent($this->app['kernel'], $event->getRequest(), $event->getRequestType(), $response);
            $this->app['dispatcher']->dispatch(KernelEvents::VIEW, $viewEvent);

            if ($viewEvent->hasResponse()) {
                $event->setResponse($viewEvent->getResponse());
            }
        }
    }
}
