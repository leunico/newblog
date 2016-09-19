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

}