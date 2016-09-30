<?php
namespace app\Controllers;

use Alicecore\Controller as BaseCollection;

class Controller extends BaseCollection
{
    // 控制器基类扩展...

    public function pageNavManage($count, $scree)
    {
        $page = $this->getPaginator();
        foreach ($scree as $key => $value) {
            if(empty($value))
                unset($scree[$key]);
        }
        return $page->pageNavManage($count, $scree, $this->pagesize);
    }

    public function pageNavComment($count, $pagesize = 10)
    {
        $page = $this->getPaginator();
        $pagesize = $pagesize ? $pagesize : $this->pagesize;
        return $page->pageNavComment($count, $pagesize);
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

    public function success($jumpurl, $msg, $target="")
    {
        $this->parameters['ms'] = 5;
        $this->parameters['referer'] = $this->getRequest()->server->get('HTTP_REFERER');
        $this->parameters['jumpurl'] = $jumpurl;
        $this->parameters['msg'] = $msg;
        $this->parameters['target'] = $target;
        return $this->render('admin/public/show_success_message', $this->parameters);
    }

    public function error($jumpurl, $msg, $target="")
    {
        $this->parameters['ms'] = 5;
        $this->parameters['referer'] = $this->getRequest()->server->get('HTTP_REFERER');
        $this->parameters['jumpurl'] = $jumpurl;
        $this->parameters['msg'] = $msg;
        $this->parameters['target'] = $target;
        return $this->render('admin/public/show_error_message', $this->parameters);
    }

    public function validation($rules)
    {
        $input = $this->getRequest()->request->all();
        $validator = $this->getValidation()->make($input, $rules);
        if(!$validator->passes()){
            $errors = $validator->messages()->all(); # 或者 $validator->errors();
            return $this->error('goback', $errors[0]);
        }

        return $input;
    }

}