<?php
namespace Alicecore\Provider;

use Pimple\Container;

interface BootableProviderInterface
{
    public function boot(Container $app);
}
