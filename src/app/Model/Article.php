<?php
namespace app\Model;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Query\Expression;

class Article extends BaseModel
{
    protected $table = 'info_article';

    protected $guarded = ['dosubmit']; #黑名单，白名单是 $fillable

    public function comments()
    {
        return $this->hasMany('app\Model\Comment', 'aid');
    }

    public function tags()
    {
        return $this->belongsToMany('app\Model\Tag'); // 'article_tag', 'tag_id', 'article_id'
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