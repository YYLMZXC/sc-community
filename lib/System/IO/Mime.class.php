<?php
class Mime{
    public static $arr=[
        'bmp'=>'image/bmp',
        'jpg'=>'image/jpeg',
        'jpeg'=>'image/jpeg',
        'png'=>'image/x-png',
        'html'=>'text/html',
        'html'=>'text/html',
        'txt'=>'text/plain',
        'mp3'=>'audio/mpeg',
        'gz'=>'application/x-gzip',
        'zip'=>'application/zip',
        'js'=>'application/x-javascript',
        'css'=>'text/css',
        'avi'=>'video/x-msvideo'
        ];
    public static function get($n){
        if(!empty(self::$arr[$n]))return self::$arr[$n];
        else return "application/octet-stream";
    }
}