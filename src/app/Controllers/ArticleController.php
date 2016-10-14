<?php
namespace app\Controllers;

use app\Model\Article;
use app\Model\Tag;
use Illuminate\Database\Query\Expression as raw;

class ArticleController extends Controller
{
    private $rules = [
            'title' => 'required|min:10|max:35',
            'seo_title' => 'required|min:2|max:20',
            'seo_description' => 'required|max:120',
            'seo_keywords' => 'required|max:8',
            'author' => 'required',
            'description' => 'required|min:60|max:160',
            'tag' => 'required',
            'mid' => 'required',
            'recommend_type' => 'required',
            'content' => 'required',
            'good_num' => 'numeric',
        ];

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

    public function delete($id)
    {
        $info = Article::find($id);
        if(empty($info))
            return $this->error('manage/articles', '没有这条数据！');

        $info->delete();
        return $this->success('manage/articles', '数据删除成功！');
    }

    public function edit($id)
    {
        $this->parameters['articles'] = Article::find($id);
        if($this->getRequest()->getMethod() == 'POST'){
            $info = $this->parameters['articles'];
            $input = $this->validation(array_merge($this->rules, ['bad_num' => 'numeric', 'clicks' => 'numeric']));
            if(!is_array($input))
                return $input;

            $article = $info->update($input);
            $oldtags = array_map(function($item){return $item->tag;}, $info->tags->all());
            $tags = $this->getTagDiff(explode('，', $input['tag']), $oldtags);

            if(!empty($tags)){
                foreach($tags as $tag){
                    if(in_array($tag, $oldtags)){
                        $detachtag = $info->tags()->where('tag', $tag)->first();
                        $info->tags()->detach($detachtag->id);
                    }else{
                        $add = Tag::firstOrCreate(['tag' => $tag]);
                        $add->increment('num');
                        $info->tags()->attach($add->id);
                    }
                }
            }

            return $this->success('manage/articles', '数据修改成功！');
        }

        return $this->render('admin/article_edit', $this->parameters);
    }

    public function save()
    {
        $input = $this->validation($this->rules);
        if(!is_array($input))
            return $input;

        $tags = explode('，', $input['tag']);
        $input['ctime'] = time();
        #$input['content'] = $this->toolContent($input['content']);
        $article = Article::create($input);
        if(empty($article))
            return $this->error('goback', '数据添加失败！');

        foreach($tags as $tag){
            $add = Tag::firstOrCreate(['tag' => $tag]);
            $add->increment('num');
            $article->tags()->attach($add->id);
        }

        return $this->success('manage/articles', '数据添加成功！');
    }

    public function show($id)
    {
        $this->parameters['ArticleList'] = Article::leftJoin('info_comment', 'info_article.id', '=', 'info_comment.aid')
        ->select(new raw('COUNT(info_comment.id) as count, info_article.*'))
        ->groupBy('info_article.id')
        ->where('uid', $id)
        ->orderBy('ctime', 'desc')
        ->skip($this->getLimit())->take($this->pagesize)
        ->get();
        $this->parameters['pageNav'] = $this->pageNavManage(Article::where('uid', $id)->count());

        return $this->render('admin/article_my', $this->parameters);
    }

}
