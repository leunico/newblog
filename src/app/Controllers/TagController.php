<?php
namespace app\Controllers;

use app\Model\Tag;
use app\Model\Article;
use Illuminate\Database\Query\Expression as raw;

class TagController extends Controller
{
    public function showAll()
    {
        $this->parameters['Tagall'] = Tag::all();
        return $this->render('tagallshow', $this->parameters);
    }

    public function show($id)
    {
        if(!$this->parameters = $this->getMemcache()->read($this->getRouteName().$this->getLimit()))
        {
            $this->parameters['tagarticleShow'] = Tag::find($id)->article()->leftJoin('info_comment', 'info_article.id', '=', 'info_comment.aid')
            ->select(new raw('COUNT(info_comment.id) as comcount, info_article.*'))
            ->groupBy('info_article.id')
            ->orderBy('top', 'desc')->orderBy('ctime', 'desc')
            ->skip($this->getLimit())->take($this->pagesize)
            ->get();
            $this->parameters['pageNav'] = $this->pageNav($this->parameters['tagarticleShow']->count());
            $this->parameters['tag'] = Tag::where('id',$id)->select('tag')->first()->toArray();

            $this->parameters = array_merge($this->parameters, Article::Recommend());
            $this->getMemcache()->write($this->getRouteName().$this->getLimit(), $this->parameters);
        }

        return $this->render('tagshow', $this->parameters);
    }

    public function index()
    {
        $this->parameters['tagList'] = Tag::orderBy('num', 'desc')->skip($this->getLimit())->take($this->pagesize)->get();
        $this->parameters['pageNav'] = $this->pageNavManage(Tag::all()->count());
        return $this->render('admin/tags', $this->parameters);
    }

    public function edit($id)
    {
        $this->parameters['tags'] = $tags = Tag::find($id);
        if($this->getRequest()->getMethod() == 'POST'){
            $input = $this->validation(['tag' => 'required']);
            if(!is_array($input))
                return $input;

            $tags->update($input);
            return $this->success('manage/tags', '数据修改成功！');
        }
        return $this->render('admin/tag_edit', $this->parameters);
    }

    public function delete($id)
    {
        $info = Tag::find($id);
        if(empty($info))
            return $this->error('manage/tags', '没有这条数据！');

        $info->articles()->detach($id);
        $info->delete();
        return $this->success('manage/tags', '数据删除成功！');
    }
}
