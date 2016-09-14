<?php
namespace Alicecore\EventListener;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Alicecore\AppFramework;

class MemcacheListener implements EventSubscriberInterface
{
    protected $app;

    public function __construct(AppFramework $app)
    {
        $this->app = $app;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
    }

    public function onKernelController(FilterResponseEvent $event)
    {
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST    =>  array('onKernelRequest', -128),
            KernelEvents::CONTROLLER =>  array('onKernelController', -128),
        );
    }
}
