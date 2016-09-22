<?php
namespace app\Controllers;

use app\Model\Tag;

class TagController extends Controller
{
    public function showAll()
    {
        $this->parameters['Tagall'] = Tag::all();
        return $this->render('tagallshow', $this->parameters);
    }

    public function show($id)
    {
        $this->parameters['tag'] = $tag->getTagById($id);
        $this->parameters['tagarticleShow'] = $article->getArticleTagShow($ret['tag']['tag'],$page);
        $this->parameters['pageNav'] = @array_pop($ret['tagarticleShow']);

        $this->parameters = array_merge($this->parameters, Article::Recommend());

        return $this->render('tagshow', $this->parameters);
    }
}
