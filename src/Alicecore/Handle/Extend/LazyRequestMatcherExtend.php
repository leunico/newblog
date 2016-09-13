<?php
namespace Alicecore\Handle\Extend;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

class LazyRequestMatcherExtend implements RequestMatcherInterface
{
    private $factory;

    public function __construct(\Closure $factory)
    {
        $this->factory = $factory;
    }

    public function getRequestMatcher()
    {
        $matcher = call_user_func($this->factory);
        if (!$matcher instanceof RequestMatcherInterface) {
            throw new \LogicException("Factory supplied to LazyRequestMatcher must return implementation of Symfony\Component\Routing\RequestMatcherInterface.");
        }

        return $matcher;
    }

    public function matchRequest(Request $request)
    {
        return $this->getRequestMatcher()->matchRequest($request);
    }
}
