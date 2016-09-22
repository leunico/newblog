<?php
namespace app\Controllers;

use app\Model\Article;
use Illuminate\Database\Query\Expression as raw;

class MenuController extends Controller
{
    public function index($name)
    {
        $this->parameters['menuclass'] = $this->container->loadconfig('menu')['menu_class'];
        $this->parameters['articleclass'] = $this->container->loadconfig('article')['article_class'];
        $this->parameters['nav'] = $name;

        if(isset($this->parameters['menuclass'][$name])){
            if(is_array($menus = $this->parameters['menuclass'][$name])){
                array_shift($menus);
                $name = array_flip($menus);
            }
        }else{
            throw new \InvalidArgumentException("Menu '$name' not Exists!");
        }

        if(!$this->getMemcache()->read($this->getRouteName().$this->getLimit()))
        {
            $where = is_array($name) ? "whereIn" : "where";
            $this->parameters['articleClassList'] = Article::leftJoin('info_comment', 'info_article.id', '=', 'info_comment.aid')
            ->select(new raw('COUNT(info_comment.id) as comcount, info_article.*'))
            ->$where('info_article.mid', $name)
            ->groupBy('info_article.id')
            ->orderBy('ctime', 'desc')
            ->skip($this->getLimit())->take($this->pagesize)
            ->get();

            $this->parameters['pageNav'] = $this->pageNav(Article::$where('info_article.mid', $name)->count());
            $this->getMemcache()->write($this->getRouteName().$this->getLimit(), $this->parameters);
        }

        return $this->render('newclassshow', $this->parameters);

    }
}
