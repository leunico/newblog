<?php
namespace app\Controllers;

class ManageController extends Controller
{
    public function index()
    {
        return $this->render('admin/index', $this->parameters);
    }

    public function push()
    {
        if($this->getRequest()->getMethod() == 'POST'){
            $fields = $this->getRequest()->request->get('pushbaidu', '');
            $api = $this->get('baidu_site_api');
            $ch = curl_init();
            $options =  array(
                CURLOPT_URL => $api,
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => implode("\n", $fields),
                CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
            );
            curl_setopt_array($ch, $options);
            $result = curl_exec($ch);
            if(strpos($result,'success'))
                return $this->success('goback', '成功推送'.$result);
            else
                return $this->error('goback', '推送失败'.$result);
        }

        return $this->render('admin/baidusite', $this->parameters);
    }

    public function update()
    {
        $session = $this->getSession();
        $lasttime = $session->get('MemacheData') ? $session->get('MemacheData') : 1444895435;
        if(time() < ($lasttime + 5)){
            return $this->error('goback', '操作过于频繁');
        }else{
            if($this->get('memcache_switch')){
                $this->getMemcache()->getMemcached()->clear();
            }else{
                return $this->error('goback', '网站未开启缓存');
            }
            $session->set('MemacheData', time());
       }

       return $this->success('goback', '缓存更新成功！');
    }

}
