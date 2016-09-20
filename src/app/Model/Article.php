<?php
namespace app\Model;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Query\Expression;

class Article extends BaseModel
{
    protected $table = 'info_article';

    public function comments()
    {
        return $this->hasMany('app\Model\Comment', 'aid');
    }

    public static function Recommend()
    {
        $parameters = [];
        $parameters['pushArticleList'] = Article::where('recommend_type', '<', 2)
            ->select('title', 'id', 'ctime', 'image')
            ->orderBy('recommend_type','asc')->orderBy('ctime','asc')
            ->take(6)
            ->get();

        $parameters['newArticleList'] = Article::select('title', 'id', 'ctime', 'image')
            ->orderBy('ctime', 'desc')
            ->take(6)
            ->get();

        $parameters['commentArticleList'] = Article::Join('info_comment', 'info_article.id', '=', 'info_comment.aid')
            ->select(new Expression('info_article.ctime,info_article.image,info_article.title,info_article.id,COUNT(info_comment.id) AS count'))
            ->groupBy('info_comment.id')
            ->orderBy('count', 'desc')
            ->take(6)
            ->get();

        return $parameters;
    }

}