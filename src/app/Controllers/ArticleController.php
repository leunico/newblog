<?php
namespace app\Controllers;

use app\Model\Article;
use Illuminate\Database\Query\Expression as raw;

class ArticleController extends Controller
{
    public function index()
    {
        $scree = $where = [];
        $scree['keyword'] = trim($this->getRequest()->query->get('keyword', ''));
        $scree['num'] = $this->getRequest()->query->get('num', '');
        $scree['recommend_type'] = $this->getRequest()->query->get('recommend_type', '');

        if(!empty($scree['recommend_type'])){
            if($scree['recommend_type'] == 3)
                $where['top'] = 1;
            else
                $where['recommend_type'] = $scree['recommend_type'];
        }

        $this->parameters['scree'] = $scree;
        $article = Article::leftJoin('info_comment', 'info_article.id', '=', 'info_comment.aid')
        ->select(new raw('COUNT(info_comment.id) as count, info_article.*'))
        ->groupBy('info_article.id')
        ->where($where)
        ->orderBy($scree['num'] ? $scree['num'] : 'ctime', 'desc')
        ->skip($this->getLimit())->take($this->pagesize);
        $count = Article::where($where);

        if(!empty($scree['keyword'])){
            $article->where('title', 'LIKE', '%'.$scree['keyword'].'%');
            $count->where('title', 'LIKE', '%'.$scree['keyword'].'%');
        }
        $this->parameters['ArticleList'] = $article->get();

        $this->parameters['pageNav'] = $this->pageNavManage($count->count(), $scree);
        return $this->render('admin/articles', $this->parameters);
    }

    public function add()
    {
        return $this->render('admin/article_add', $this->parameters);
    }

    public function save()
    {
        $rules = [
            'title' => 'required|min:5|max:25',
            'seo_title' => 'required|min:5|max:50',
            'seo_description' => 'required|min:10',
            'seo_keywords' => 'required|max:20',
            'author' => 'required',
            'description' => 'required|min:20',
            'tag' => 'required',
            'mid' => 'required',
            'recommend_type' => 'required',
            'content' => 'required',
            'good_num' => 'required|numeric',
        ];

        $input = $this->validation($rules);
var_dump($input);exit();
    }

}
