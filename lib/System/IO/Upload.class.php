<?php
class Upload{
    private $acceptTypes=['*'];
    private $sizeType="auto";
    private $host="";//域名
    private $fixpath="";
    private $needDelPath="";
    public function __construct(){
        $this->host="https://".$_SERVER['HTTP_HOST'];
    }
    //设定域名
    public function setHost($host){
        $this->host=$host;
        return $this;
    }
    //设置路径修正值
    public function setFixPath($fix){
        $this->fixpath=$fix;
        return $this;
    }
    //设置需要删除的路径
    public function setNeedDelPath($str){
        $this->needDelPath=$str;
    }
    //获取下载路径
    public function getDownUrl($filepath){
        $descdirArr=[];$tempDir="";
        $descdirArr=explode("/",$filepath);
        foreach($descdirArr as $eachDir){
            if($eachDir==".."||$eachDir==".")continue;
            else $tempDir.=$eachDir."/";
        }
        $tempDir=substr($tempDir,0,strlen($tempDir)-1);
        $tmp=$this->host.$this->fixpath."/".$tempDir;
        if(!empty($this->needDelPath)){
            $d=explode($this->needDelPath,$tmp);
            return $d[0].$d[1];
        }
        return $tmp;
    }
    //设定文件大小的格式化类型
    public function setSizeType($type){
        $this->sizeType=$type;
        return $this;
    }
    //设定支持的文件类型
    public function setAcceptType($typearrs){
        if(is_array($typearrs)){
            $this->acceptTypes=$typearrs;
        }
        return $this;
    }
    //获得文件后缀
    public function getPostfix($filename){
        $split=explode(".",$filename);
        return $split[count($split)-1];
    }
    /**
     * @param $upload $_FILE['参数']
     * @param $dir 保存文件路径
     */
    function save($upload,$dir="./uploads"){
        $info=array();
        $name=$upload['name'];
        $postFix=$this->getPostfix($name);$flag=false;
        $ftype=$postFix;
        
        if(empty($postFix)){
            return ['code'=>1,'msg'=>"不支持的文件类型"];
        }
        foreach($this->acceptTypes as $type){
            if($type==$postFix||$type=='*'){
                $flag=true;
                break;
            }
        }
        if(!$flag){//文件类型不符
            return ['code'=>1,'msg'=>"不支持的文件类型;".$name];
        }
        $fsize=$upload['size'];
        if($fsize==0){
            return ['code'=>2,'msg'=>"不能上传空文件"];
        }
        //$randname=time().rand(1000,9999).".".$ftype;
        $randname=$upload['name'];
        $dir.=date("/Y/m/d",time());
        if(!is_dir($dir))mkdir($dir,0777,true);
        $f=file_get_contents($upload['tmp_name']);
        file_put_contents($dir."/".$randname,$f);
        unlink($upload['tmp_name']);
        if(substr($dir,0,3)!="../")$info['filepath']=$dir."/".$randname;
        else $info['filepath']="./".substr($dir,3,strlen($dir)-1)."/".$randname;
        $sizetype=strtolower($this->sizeType);
        $info['filesize']=$fsize;
        $info['downUrl']=$this->getDownUrl($info['filepath']);
        switch ($sizetype) {
            case 'b':
                $info['rsize']=$fsize." B";
                break;
            case 'kb':
                $info['rsize']=floor($fsize/1024)." KB";
                break;
            case 'mb':
                $info['rsize']=floor($fsize/1024/1024)." MB";
                break;
            case 'gb':
                $info['rsize']=floor($fsize/1024/1024/1024)." GB";
                break;
            case 'auto':
                if($fsize<1024){
                    $info['rsize']=floor($fsize)." B";
                }
                elseif($fsize>=1024&&$fsize<1024*1024){
                    $info['rsize']=floor($fsize/1024)." KB";
                }elseif($fsize>=1024*1024&&$fsize<1024*1024*1024){
                    $info['rsize']=floor($fsize/1024/1024)." MB";                
                }else{
                    $info['rsize']=floor($fsize/1024/1024/1024)." GB";
                }
                break;
            default:
                $info['rsize']="0 B";
                break;
        }
        $info['name']=$name;
        $info['code']=$upload['error'];
        return $info;
    }
}
?>