<?php
class Upload{
    //获取上传文件的参数
    public static function Arg(){
        $d = $_SERVER['HTTP_DROPBOX_API_ARG'];
        $e = json_decode($d,true);
        return $e;
    }
    //获取RAW资源
    public static function getPostRaw(){
        return file_get_contents('php://input');
    }
}