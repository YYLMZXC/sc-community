<?php
class Path{
    public $path;
    public function __construct($str){
        $this->path=$str;
    }
    public function getDirs(){
        $res=[];
        $str=str_replace('\\','/',$this->path);
        $arr=explode('/',$str);
        foreach($arr as $k=>$v){
            if(empty($v))continue;
            $res[]=$v;
        }
        return $res;
    }
    public function getSavePath($user,$filename){
        $prifix="/www/wwwroot/cdn.aijiajia.xyz/gamefiles/".$user;//后面不带/
        $savepath=$prifix.$filepath->path;//储存路径
        return $savepath."/".$filename;
    }
    public function getParentDir(){//获取上级目录
        $res=$this->getDirs();
        if(count($res)==0)return '/';
        $di='/';
        for($i=0;$i<count($res)-2;$i++){
            $di.=$res[$i]."/";
        }
        return $di;
    }
    public function getPath(){
        $res=$this->getDirs();
        if(count($res)==0)return '/';
        $di='/';
        for($i=0;$i<count($res)-1;$i++){
            $di.=$res[$i]."/";
        }
        return $di;
    }
    public function getSCType(){
        if($this->isDir())return 0;//folder
        $type=$this->getExtension();
        switch ($type) {
            case '.scworld'://world
                return 1;
                break;
            case '.scbtex'://blocktexture
                return 2;
                break;
            case '.scskin'://CharacterSkin
                return 3;
                break;
            case '.scfpack'://FurniturePack
                return 4;
                break;
            default:
                return 0;//Directory
                break;
        }
    }
    public static function TypeToSC($type){
        switch($type){
            case 0:
                return 'Directory';
            case 1:
                return 'World';
            case 2:
                return 'BlocksTexture';
            case 3:
                return 'CharacterSkin';
            case 4:
                return 'FurniturePack';
                default:return 'Directory';
        }
        
    }
    public function getExtension(){
        $res=$this->getDirs();
        if(count($res)==0)return $res;
        $end=$res[count($res)-1];
        if(strstr($end,'?')){
            $d=explode('?',$end);
            if(strstr($d[0],'.')){
                $e=explode('.',$d[0]);
                return $e[count($e)-1];
            }else return $d[0];
        }
        if(strstr($end,'.')){
            $arr=explode('.',$end);
            return '.'.$arr[count($arr)-1];
        }else{
            return $end;
        }
    }
    public function isDir(){
        $res=$this->getDirs();
        if(count($res)==0)return false;
        $end=$res[count($res)-1];
        if(strstr($end,'.'))return false;
        else return true;
    }
    public function isFile(){
        $res=$this->getDirs();
        if(count($res)==0)return false;
        $end=$res[count($res)-1];
        if(strstr($end,'.'))return true;
        else return false;
    }
    public function getFileName(){
        $res=$this->getDirs();
        if(count($res)==0)return $res;
        $end=$res[count($res)-1];
        if(strstr($end,'.')){
            $arr=explode('.',$end);
            $fn="";
            for($i=0;$i<count($arr)-1;$i++){
                $fn.=$arr[$i].".";
            }
            return substr($fn,0,strlen($fn)-1);
        }else{
            return $end;
        }
    }
    public function getFullName(){
        $res=$this->getDirs();
        if(count($res)==0)return $res;
        $end=$res[count($res)-1];
        return $end;
    }
 }