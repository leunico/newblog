<?php
namespace Alicecore\EventListener;

use Symfony\Component\Debug\ExceptionHandler as DebugExceptionHandler;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionListener implements EventSubscriberInterface
{
    protected $debug;

    public function __construct($debug)
    {
        $this->debug = $debug;
    }

    public function onAlicecoreError(GetResponseForExceptionEvent $event)
    {
        $handler = new DebugExceptionHandler($this->debug);
echo 'symfony的抛出错误显示：';
        $exception = $event->getException();
        if (!$exception instanceof FlattenException) {
            $exception = FlattenException::create($exception);
        }

        $response = Response::create($handler->getHtml($exception), $exception->getStatusCode(), $exception->getHeaders())->setCharset(ini_get('default_charset'));

        $event->setResponse($response);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(KernelEvents::EXCEPTION => array('onAlicecoreError', -255));
    }
}
