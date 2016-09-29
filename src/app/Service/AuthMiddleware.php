<?php
namespace app\service;

use Alicecore\AppFramework;
use Alicecore\Handle\Extension\MiddlewareInterface;
use Symfony\Component\HttpFoundation\Request;
use app\Model\Article;

class AuthMiddleware implements MiddlewareInterface
{
    protected $app;

    public function boot(Request $request, AppFramework $app)
    {
        $this->app = $app;
        $logininfo = $app->getSession()->get($app['session_pre'].'admin_user_login');
        if(empty($logininfo) || !isset($logininfo['username']) || !isset($logininfo['id']))
            return $app->render('admin/public/show_error_message', ['jumpurl' => 'login', 'msg' => '对不起，你还没有登录！']);

        if(isset($logininfo['block']) && $logininfo['block'] == 1){
            $session->clear();
            return $app->render('admin/public/show_error_message', ['jumpurl' => '', 'msg' => '你的帐号被管理员拉黑了！']);
        }
    }

    public function security(Request $request, AppFramework $app)
    {
        $this->boot($request, $app);
        /*$loginInfo = Request::getSession('admin_user_login');
        $admin = self::$models->Admin;
        $result = $admin->getByUserId($loginInfo['id']);
        if($id !== ''){
            $article = $admin->getByArticleId($id);
            if($article['uid'] !== $loginInfo['id'] && $result['is_admin'] == '0') View::AdminErrorMessage('goback', '你没有这篇文章的操作权限！');
            return $result['is_admin'];
        }else{
            if($result['is_admin'] == '0') View::AdminErrorMessage('goback', '对不起，你没有这个操作的权限！');
        }*/
    }
}