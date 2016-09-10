<?php
namespace app\Controllers;

use app\Model\Article;
use app\Model\Tag;
use app\Model\Push;
use app\Model\Comment;
use Illuminate\Database\Query\Expression as raw;

class IndexController extends Controller
{
    public function index()
    {
        if(!$this->parameters = $this->getMemcache()->read($this->getRouteName().$this->getLimit()))
        {
            $this->parameters['articleList'] = Article::leftJoin('info_comment', 'info_article.id', '=', 'info_comment.aid')
            ->select(new raw('COUNT(info_comment.id) as comcount, info_article.*'))
            ->groupBy('info_article.id')
            ->orderBy('top', 'desc')->orderBy('ctime', 'desc')
            ->skip($this->getLimit())
            ->take($this->pagesize)
            ->get();
            $this->parameters['pageNav'] = $this->pageNav(Article::all()->count());
            $this->parameters['pushArticleList'] = Article::where('recommend_type', 2)
            ->select('id','image','title','mid','author')
            ->orderBy('good_num', 'desc')->orderBy('ctime', 'desc')
            ->take(6)
            ->get();
            $this->parameters['tagList'] = Tag::where([])->orderBy('num', 'desc')->take(15)->get();
            $this->parameters['pushIndex'] = Push::where([])->orderBy('utime', 'desc')->take(4)->get();

            $this->getMemcache()->write($this->getRouteName().$this->getLimit(), $this->parameters);
        }
        
        $this->parameters['commentList'] = Comment::Join('info_article', 'info_article.id', '=', 'info_comment.aid')
        ->select('info_comment.*', 'info_article.title')
        ->orderBy('ctime', 'desc')
        ->take(5)
        ->get();
        //var_dump($this->parameters['articleList']->count());die();
        return $this->render('newindex', $this->parameters);

    }

    public function cms()
    {
        echo 'test>>>>';

        $route = $this->get('routes')->get('GET_memsss');
        var_dump($route);
        //$ip = $this->getRequest()->getClientIps();
        //var_dump($ip);      
        die();
    }

}
