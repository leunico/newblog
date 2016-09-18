<?php
namespace app\Controllers;

use Alicecore\Controller as BaseCollection;

class Controller extends BaseCollection
{
    // 控制器基类扩展...

    public function pageNavAdmin($count)
    {
        # code...
    }

    public function pageNavComment($count)
    {
        # code...
    }

    public function getLimit()
    {
        $page = $this->page;
        return ($page-1)*$this->pagesize;
    }

    public function getRouteName()
    {
        if($this->getRequest()->attributes->get('_route_params')){
            return $this->getRequest()->attributes->get('_route') . "_" . implode('_', $this->getRequest()->attributes->get('_route_params'));
        }else{
            return $this->getRequest()->attributes->get('_route');
        }
    }

}