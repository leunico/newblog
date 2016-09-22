<?php
namespace app\service;

use Alicecore\AppFramework;
use app\Model\Comment;
use Alicecore\Handle\Extension\ServiceInterface;

class ControllerService implements ServiceInterface
{
    private $app;

    public function __construct(AppFramework $app)
    {
        $this->app = $app;
    }

    public function commentToArray($comments, $limit)
    {
        $kidid = $data = [];
        $i = $comments->count() - $limit;
        foreach($comments->toArray() as $k=>$v){
            $kidid[] = $v['id'];
            $v['louc'] = $i--;
            $data[$v['id']] = $v;
        }

        $kid = Comment::whereIn('cid', $kidid)->orderBy('ctime','DESC')->get()->toArray();
        foreach($kid as $key=>$val){
            if(!empty($val['parent'])){
                $parents = explode(',',$val['parent']);
                $val['pid'] = $parents[0];
                $val['pnickname'] = $parents[1];
            }
            unset($val['parent']);
            foreach($data as $k=>$v){
                if($val['cid'] == $k){
                    $data[$k]['son'][] = $val;
                }
            }
        }
        return $data;
    }

    public function ipValidator($ip)
    {
        $localtime = time() - 12*3600;
        $validator = Comment::where('ip', $ip)->where('ctime', '>', $localtime)->count();
        return $validator > 12 ? true : false;
    }

    public function touristCookie($username, $email, $weburl)
    {
        $cookies = $this->app['request_stack']->getCurrentRequest()->cookies;
        $username_ = $cookies->get('comment_username');
        $email_ = $cookies->get('comment_email');
        $weburl_ = $cookies->get('comment_weburl');
        if($username_ !== $username || $email_ !== $email){
            setcookie('comment_username', $username, time()+86400*3);
            setcookie('comment_email', $email, time()+86400*3);
            setcookie('comment_weburl', $weburl, time()+86400*3);
        }
    }

}