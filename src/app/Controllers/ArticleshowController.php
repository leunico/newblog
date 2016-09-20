<?php
namespace app\Controllers;

use app\Model\Article;

class ArticleshowController extends Controller
{
    public function index($id)
    {
        if(!$this->parameters = $this->getMemcache()->read($this->getRouteName()))
        {
            $this->parameters['articleShow'] = Article::find($id);
            $this->parameters['articleShow']['up'] = Article::where('ctime', '>', $this->parameters['articleShow']['ctime'])->select('title', 'id')->orderBy('ctime','asc')->first();
            $this->parameters['articleShow']['down'] = Article::where('ctime', '<', $this->parameters['articleShow']['ctime'])->select('title', 'id')->orderBy('ctime','asc')->first();
            $this->parameters['articleShow']['tag'] = explode('|', $this->parameters['articleShow']['tag']);

            $this->parameters['articleRelevant'] = Article::where('mid', $this->parameters['articleShow']['mid'])
            ->where('mid', '!=', $this->parameters['articleShow']['id'])
            ->select('title', 'id', 'image')
            ->orderBy('good_num', 'desc')
            ->take(4)
            ->get();

            $this->parameters = array_merge($this->parameters, Article::Recommend());
            $this->getMemcache()->write($this->getRouteName(), $this->parameters);
        }

        $this->parameters['comments'] = $this->getViceController()->commentToArray(Article::find($id)->comments()
        ->where('cid', 0)
        ->orderBy('ctime', 'desc')
        ->skip($this->getLimit())->take($this->pagesize)
        ->get(), $this->getLimit());
        $this->parameters['articleShow']['counts'] = Article::find($id)->comments()->where('cid', 0)->count();
        $this->parameters['articleShow']['commentPagenav'] = $this->pageNavComment($this->parameters['articleShow']['counts']);

        Article::increment('clicks');
        return $this->render('articleshow', $this->parameters);
    }

}
