<?php
namespace Alicecore\Handle\Extension;

use Alicecore\AppFramework;
use Symfony\Component\HttpFoundation\Request;

interface MiddlewareInterface
{
    public function boot(Request $request, AppFramework $app);
}
