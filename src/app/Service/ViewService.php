<?php
namespace app\service;

use Alicecore\AppFramework;
use Alicecore\Handle\Extension\ServiceInterface;

class ViewService implements ServiceInterface
{
    private $app;

    public function __construct(AppFramework $app)
    {
        $this->app = $app;
    }

    public function getBaseUrl()
    {
        $baseUrl = str_replace('', '/', dirname($this->getServer('SCRIPT_NAME')));
        $website = empty($baseUrl) ? '/' : trim($baseUrl, '/');
        return $website;
    }

    private function getStatic($type, $name)
    {
        $file_dir = $this->app['static'].$type;
        $file_name = '/'.$name;

        if ($name && !file_exists($file_dir.$file_name)) {
            throw new \InvalidArgumentException("Static file '$file_name' not Exists!");
        }

        return $this->getBaseUrl().$type.$file_name;
    }

    public function Route($route)
    {
        return 'http://'.$this->app['request_stack']->getCurrentRequest()->server->get('HTTP_HOST').$this->getBaseUrl().$route;
    }

    public function getConfig($name)
    {
        return $this->app->loadconfig($name);
    }

    public function getServer($name)
    {
        return $this->app['request_stack']->getCurrentRequest()->server->get($name);
    }

    public function wordTime($time)
    {
        $time = (int) substr(strtotime($time), 0, 10);
        $int = time() - $time;
        $str = '';
        if ($int <= 2){
            $str = sprintf('刚刚', $int);
        }elseif ($int < 60){
            $str = sprintf('%d秒前', $int);
        }elseif ($int < 3600){
            $str = sprintf('%d分钟前', floor($int / 60));
        }elseif ($int < 86400){
            $str = sprintf('%d小时前', floor($int / 3600));
        }elseif ($int < 2592000){
            $str = sprintf('%d天前', floor($int / 86400));
        }else{
            $str = date('Y-m-d H:i:s', $time);
        }
        return $str;
	}

    public function getThumb($content, $order='ALL')
    {
        $pattern="/<img.*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/";
        preg_match_all($pattern,$content,$match);

        if(isset($match[1]) && !empty($match[1])){
            if($order==='ALL')
                return $match[1];

            if(is_numeric($order) && isset($match[1][$order]))
                return $match[1][$order];
        }

        return '';
    }

    public function getCss($name)
    {
        return $this->getStatic('css', $name);
    }

    public function getJs($name)
    {
        return $this->getStatic('js', $name);
    }

    public function getImage($name)
    {
        return $this->getStatic('image', $name);
    }

    public function getPlug($name, $file = '')
    {
        return $this->getStatic('plug/'.$name, $file);
    }

    public function escape($str)
    {
        return $this->app->escape($str);
    }

    public function EmojiH($msg){
        $emoji = [
            ':mrgreen:',':razz:',':smile:',':oops:',':grin:',':lol:',
            ':neutral:',':idea:',':wink:',':?:',':arrow:',':sad:',':cry:',
            ':eek:',':surprised:',':???:',':cool:',':mad:',':twisted:',':roll:',':evil:',':!:'
            ];

        $baseUrl = $this->getImage('smilies').'/';
        $emojiUrl = [
            '<img src="'.$baseUrl.'icon_mrgreen.gif">',
            '<img src="'.$baseUrl.'icon_razz.gif">',
            '<img src="'.$baseUrl.'icon_smile.gif">',
            '<img src="'.$baseUrl.'icon_redface.gif">',
            '<img src="'.$baseUrl.'icon_biggrin.gif">',
            '<img src="'.$baseUrl.'icon_lol.gif">',
            '<img src="'.$baseUrl.'icon_neutral.gif">',
            '<img src="'.$baseUrl.'icon_idea.gif">',
            '<img src="'.$baseUrl.'icon_wink.gif">',
            '<img src="'.$baseUrl.'icon_question.gif">',
            '<img src="'.$baseUrl.'icon_arrow.gif">',
            '<img src="'.$baseUrl.'icon_sad.gif">',
            '<img src="'.$baseUrl.'icon_cry.gif">',
            '<img src="'.$baseUrl.'icon_eek.gif">',
            '<img src="'.$baseUrl.'icon_surprised.gif">',
            '<img src="'.$baseUrl.'icon_confused.gif">',
            '<img src="'.$baseUrl.'icon_cool.gif">',
            '<img src="'.$baseUrl.'icon_mad.gif">',
            '<img src="'.$baseUrl.'icon_twisted.gif">',
            '<img src="'.$baseUrl.'icon_roll.gif">',
            '<img src="'.$baseUrl.'icon_evil.gif">',
            '<img src="'.$baseUrl.'icon_exclaim.gif">'
            ];

        $msg = str_replace($emoji, $emojiUrl, $msg);
        return $msg;
    }

    public function getUser($type)
    {
        $key = $this->app['session_pre'].'admin_user_login';
        $loginInfo = $this->getSession()->get($key);
        if($type === 'all')
            return $loginInfo;

        if($type && isset($loginInfo[$type])){
            return $loginInfo[$type];
        }else{
            $key = 'comment_'.$type;
            $getcookie = $this->app['request_stack']->getCurrentRequest()->cookies->get($key);
            return $getcookie;
        }

        return false;
    }

    public function __call($method, $arguments)
    {
        return $this->app->$method($arguments);
    }

    public function get($name)
    {
        return $this->app[$name];
    }

}