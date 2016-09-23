<?php
namespace app\Controllers;

use app\Model\Article;

class SearchController extends Controller
{
    private $rules = [
                'dosubmit' => 'required',
                'search'   => 'required|min:2'
            ];

    public function index()
    {
        $input = $this->getRequest()->request->all();
        $validator = $this->getValidation()->make($input, $this->rules);
        if(!$validator->passes()){
            $errors = $validator->messages()->all();
            $this->getSession()->getFlashBag()->set('notice', $errors);
        }

        $this->parameters['keyword'] = $fields = $this->getRequest()->request->get('search');
        $this->parameters['searchList'] = Article::where('title', 'LIKE', "%$fields%")
        ->orWhere('description', 'LIKE', "%$fields%")
        ->orderBy('ctime','DESC')
        ->take(20)->get();

        $this->parameters = array_merge($this->parameters, Article::Recommend());
        return $this->render('search', $this->parameters);
    }

}
