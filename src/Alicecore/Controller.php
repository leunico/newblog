<?php
namespace Alicecore;

use Alicecore\Handle\Extension\ContainerInterface;
use Alicecore\Handle\PaginatorHandler;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller implements ContainerInterface
{
    protected $container;

    protected $parameters = [];

    protected $page;

    protected $pagesize = 10;

    public function setContainer(AppFramework $container)
    {
        $this->container = $container;

        $this->page = $this->getRequest()->query->get('page', 0);
    }

    public function has($id)
    {
        return array_key_exists($id, array_flip($this->container->keys()));
    }

    public function get($id)
    {
        return $this->container[$id];
    }

    public function getRequest()
    {
        return $this->container['request_stack']->getCurrentRequest();
    }

    public function createNotFoundException($message = 'Not Found', \Exception $previous = null)
    {
        return new NotFoundHttpException($message, $previous);
    }

    public function redirect($url, $status = 302)
    {
        return $this->container->redirect($url, $status);
    }

    public function forward($controller, array $path = array(), array $query = array())
    {
        $path['_controller'] = $controller;
        $subRequest = $this->container['request_stack']->getCurrentRequest()->duplicate($query, null, $path);
        return $this->container['http_kernel']->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }

    public function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->container['router']->generate($route, $parameters, $referenceType);
    }

    public function render($name, $parameters)
    {       
        $file_dir = dirname(dirname(__FILE__))."\\app\\View\\";
        $file_name = $name.'.tpl.php';

        if (!file_exists($file_dir.$file_name)) {
            throw new \InvalidArgumentException("Template file '$file_name' not Exists!");
        }

        $response = new Response();
        $view = $this->container['view'];
        @extract($parameters, EXTR_SKIP);

        ob_start();
        include $file_dir.$file_name;
        $response->setContent(ob_get_clean());

        if(!empty($this->container['httpcache_switch'])){
            $time = $this->container['httpcache_time'] ? (int)$this->container['httpcache_time'] : 60;
            $response->setTtl($time);
        }

        return $response;
    }

    public function getPaginator()
    {
        $routeName = $this->getRequest()->attributes->get('_route');
        return new PaginatorHandler($this->page, $routeName, $this->get('url_generator'));
    }

    public function pageNav($count)
    {
        $page = $this->getPaginator();
        return $page->pageNavIndex($count, $this->pagesize);
    }

    public function __call($method, $arguments)
    {
        return $this->container->$method($arguments);
    }

}
