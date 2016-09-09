<?php
namespace Alicecore\Provider;

use Alicecore\Application;
//use Alicecore\Handle\Resolver\ControllerCollection;

interface ControllerProviderInterface
{
    public function connect(Application $app);
}
