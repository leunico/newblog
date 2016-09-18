<?php
namespace app\service;

use Alicecore\AppFramework;
use Alicecore\Handle\Extension\MiddlewareInterface;
use Symfony\Component\HttpFoundation\Request;
use app\Model\Diary;
use app\Model\Article;
use Illuminate\Database\Query\Expression as raw;

class MenuMiddleware implements MiddlewareInterface #未触发Controller事件，不能继承控制器类
{
    protected $args = [
            'timewait' => 'twait',
            'me' => 'aboutme',
            'meclass' => 'aboutme',
            'articlebox' => 'articlebox',
            'liuy' => 'liuy',
        ];
    protected $app;

    public function boot(Request $request, AppFramework $app)
    {
        $class = $request->attributes->get('name');
        $this->app = $app;
        if(array_key_exists($class, $this->args))
            return $app->render($this->args[$class], $this->$class($class));
    }

    private function timewait($class)
    {
        return ['timewait' => Diary::orderBy('order','ASC')->take(20)->get()];
    }

    private function articlebox($class)
    {
        if(!$result = $this->app['memcache']->read($class)){
            $resulta = Article::select(new raw("FROM_UNIXTIME( ctime,'%Y-%m' ) AS pubtime, COUNT( * ) AS count"))
                ->groupBy('pubtime')
                ->orderBy('ctime', 'desc')
                ->take(30)
                ->get();
            $resultb = Article::leftJoin('info_comment', 'info_article.id', '=', 'info_comment.aid')
                ->select(new raw("FROM_UNIXTIME( info_article.ctime,'%Y-%m' ) AS pubtime, COUNT( info_comment.id ) AS count, info_article.title, info_article.id, info_article.ctime"))
                ->groupBy('info_article.id')
                ->take(30)
                ->get();

            foreach($resulta as $ret){
                foreach($resultb as $val){
                    if( $ret['pubtime'] == $val['pubtime']){
                        $result[$ret['pubtime']][] = $val;
                    }
                }
            }
            $this->app['memcache']->write($class, $result);
        }
        return ['articleClassList' => $result];
    }

    public function __call($method, $arguments)
    {
        return [];
    }
}