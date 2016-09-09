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
		return $this->getRequest()->attributes->get('_route');
	}
}