<?php
namespace app\service;
require_once dirname(dirname(dirname(__DIR__))).'\web\plug\qiniu\autoload.php';

use Qiniu\Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
use Alicecore\AppFramework;
use Alicecore\Handle\Extension\ServiceInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class QiNiuService implements ServiceInterface
{

    private $accessKey = 'BfR2VxdEwZavgGM3kVChc6F0NKwGMTPMUdUsywtz';

    private $secretKey = 'jhifcIoBhyH_AQBsxAHChXaR9nWMed3F07pOI4JM';

    private $bucket = "lzxya";

    private $auth = "";

    public function __construct(AppFramework $app)
    {
        $this->app = $app;
        $this->auth = new Auth($this->accessKey, $this->secretKey);
    }

    private function delete($filename)
    {
        $Bucket = new BucketManager($this->auth);
        $err = $Bucket->delete($this->bucket, $filename);
        return ($err !== null) ? '' : true;
    }

    public function setBucket($bucket = null)
    {
        $this->bucket = $bucket ? $bucket : "lzxya";
        return $this;
    }

    private function put($filename, $filePath, $category)
    {
        $token = $this->auth->uploadToken($this->bucket);
        $uploadMgr = new UploadManager();
        list($ret, $err) = $uploadMgr->putFile($token, $filename, $filePath);
        return ($err !== null) ? '' : 'http://7xq09h.com1.z0.glb.clouddn.com/'.$ret['key'].$category;
    }

    public function setDiaryImg(UploadedFile $file)
    {
        $category = '?imageView2/1/w/120/h/120';
        if(!$file->getError()){
            $name = "diary_".time().'.jpg';
            return $this->put($name, $file->getPathName(), $category);
        }else{
            return false;
        }
    }

    public function setPushImg(UploadedFile $file, $oldimg)
    {
        $category = '?imageView2/1/w/810/h/200';
        if(!$file->getError()){
            $name = "push_".time().'.jpg';
            $image = $this->put($name, $file->getPathName(), $category);
            if($oldimg && $image)
                $this->delImg($oldimg);

            return $image;
        }else{
            return false;
        }
    }

    public function delImg($fileurl)
    {
        $qiniuimg = explode('/', $fileurl);
        $img = explode('?', $qiniuimg[3]);
        return $this->delete($img[0]);
    }

}