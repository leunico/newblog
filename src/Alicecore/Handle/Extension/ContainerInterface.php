<?php
namespace Alicecore\Handle\Extension;

use Alicecore\AppFramework;

interface ContainerInterface
{
    public function setContainer(AppFramework $container);
    public function get($id);
    public function has($id);
}
