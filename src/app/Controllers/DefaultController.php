<?php
namespace app\Controllers;

use app\Model\User;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function index()
    {
    	//$request = $this->getRequestStack();
    	$users = User::find(6);//User::where('is_admin', '=', 1)->get();
    	//$users = $this->getDb()->table('admin_user')->where('is_admin', '=', 1)->get();
    	//var_dump($users->username);
        $response = new Response(rand(0,10).'____hahahahahahah+++++++++++++'.$users->username);
        //$response->setTtl(8); //設置http緩存
        return $response;
    }

    public function news($name)
    {
    	//var_dump($this->getRequest()->get('name'));
    	$session = $this->getSession();
    	$session->set('_security_', 'MDZZ2333333', 60);
    	$session->getFlashBag()->set('notice', 'Your changes were saved!'); //setFlash()功能
        //$session->save();
        $route = $session->get('_security_');
        $route = $session->getFlashBag()->get('notice'); //setFlash()功能
    	var_dump($route);
        return 'SB+++++++++++++'.$name;
    }

    public function memcache()
    {
    	$memcache = $this->getMemcache();
    	$users = $memcache->write('test', 'ha我我我我');
    	$users = $memcache->read('test');
    	var_dump($users);
        return 'memcache+++++++++++++';
    }
}
