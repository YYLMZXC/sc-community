<?php

/**
 * HTTP请求封装库
 * Powered By xxq
 * <Date:2021-03-12></Date:2021-03-12>
 * 
**/
class WebRequest{
    public $url="";
    public $method="GET";
    public $noHeader=true;
    public $noBody=false;
    public $param=[];
    public $returnStream=true;
    public $header=[];
    public $useragent="Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.90 Mobile Safari/537.36";//默认为电脑Chrome浏览器
    public function __construct($u){
        $this->url=$u;
    }
    public function setHeader($h){
        $this->header=$h;
        return $this;
    }
    public function setUserAgent($u){
        $this->useragent=$u;
    }
    public function setNobody($f){
        $this->noBody=$f;
        return $this;
    }
    public function setNoHeader($f){
        $this->noHeader=$f;
        return $this;
    }
    public function setMethod($m){
        $this->method=$m;
        return $this;
    }
    //获取响应内容
    public function GetResponse(){
        $prehead=substr($this->url,0,5);
        if($prehead!="http:"&&$prehead!="https"){
            $this->url="http://".$this->url;
        }
        $c=curl_init($this->url);
        if($this->returnStream)curl_setopt($c,CURLOPT_RETURNTRANSFER,true);
        if($prehead=="https"){
            curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 1);
        }
        curl_setopt($c,CURLOPT_NOBODY,$this->noBody);
        curl_setopt($c,CURLOPT_HEADER,!$this->noHeader);
        curl_setopt($c,CURLOPT_HTTPHEADER,$this->header);
        curl_setopt($c,CURLOPT_USERAGENT,$this->useragent);
        if($this->method=="POST"){
            if(is_array($this->param)){
                $param="";
                foreach ($this->param as $k=>$v){
                    $v=urlencode($v);
                    $param.="$k=$v&";
                }
                $param=substr($param,0,strlen($param)-1);
                curl_setopt($c,CURLOPT_POSTFIELDS,$param);
            }else{
                curl_setopt($c,CURLOPT_POSTFIELDS,$this->param);
            }
        }
        return curl_exec($c);
    }
}