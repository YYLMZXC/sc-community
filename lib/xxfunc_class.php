<?php
/**
 * 
 * 
 * 全局功能函数
 * */
class Arr{
    public $arr;
    public function __construct($arr){
        if(empty($arr))$this->arr=[];
        else if(is_string($arr))$this->arr=json_decode($arr,true);
        else $this->arr=$arr;
    }
    public function Contains($arr){
        if(xxfunc::isempty($arr))return false;
        else if(is_array($arr)){
            
        }else{
            foreach($this->arr as $k=>$v){
                if($v==$arr)return true;
            }
        }
        return false;
    }
    public function Remove($arr){
        if(xxfunc::isempty($arr))return false;
        else if(is_array($arr)){
            
        }else{
            foreach($this->arr as $k=>$v){
                if($v==$arr)unset($this->arr[$k]);
            }
        }
        return false;
    }
    public function getArr(){
        return $this->arr;
    }
}

class R{
    public static $redis;
    //初始化
    public static function init($addr='127.0.0.1',$port=6379){
        try{
            self::$redis=new redis();
            if(self::$redis==null)die("your server doesn't support redis");
            $f=self::$redis->connect($addr,$port);
            if(!$f)die("can't connect redis server");
        }catch(Exception $e){
            die("your server doesn't support redis");
        }
    }
    //存入队列
    public static function lpush($n,$v){
        return self::$redis->lPush($n,$v);
    }
    //取出队列
    public static function lrange($n,$s,$e){
        return self::$redis->lRange($n,$s,$e);
    }
    public static function lsize($n){
        return self::$redis->lSize($n);
    }
    //设置key-value
    public static function set($n,$v){
        return self::$redis->set($n,$v);
    }
    //取出key-value
    public static function get($n){
        return self::$redis->get($n);
    }
    //设定一个有生存时间的key-value
    public static function setex($n,$v,$t){
        return self::$redis->setex($n,$v,$t);
    }
    //判断是否重复的，写入值,如存在了不修改返回0,不存在就添加返回1
    public static function setnx($n,$v){
        return self::$redis->setnx($n,$v);
    }
    public static function keys($n){
        return self::$redis->keys($n);
    }
    //删除key-value也可以删除整个list
    public static function delete($n){
        return self::$redis->delete($n);
    }
    //查询key-value的生存时间-1为永久
    public static function ttl($n){
        return self::$redis->ttl($n);
    }
    //输出链表最左边的ksy的值，输出后删除掉这个key
    public static function lpop($n){
        return self::$redis->lPop($n);
    }
    //输出链表最右边的ksy的值，输出后删除掉这个key
    public static function rpop($n){
        return self::$redis->rPop($n);
    }
    //向名称为h的hash中添加元素key1—>hello
    public static function hset($d,$n,$v){
        return self::$redis->hSet($d,$n,$v);
    }
    //返回名称为h的hash中元素个数
    public static function hlen($n){
        return self::$redis->hLen($n);
    }
    //返回名称为h的hash中key1对应的value（hello）
    public static function hget($d,$v){
        return self::$redis->hGet($d,$n);
    }
    //删除名称为h的hash中键为key1的域
    public static function hdel($d,$n){
        return self::$redis->hDel($d,$n);
    }
    
}
class xxfunc{
    public function __construct(){}
    public static function setLastReply($uid){
        R::setex("reply-user-".$uid,300,time());
    }
    public static function encrypt($string){
        return self::encryptLogic($string,'E');
    }


    public static function decrypt($string){
        return self::encryptLogic($string,'D');
    }
    public static function encryptLogic($string,$operation,$key='1144822034')
    {
        $src = array("/","+","=");
        $dist = array("_a","_b","_c");
        if($operation=='D'){$string = str_replace($dist,$src,$string);}
        $key=md5($key);
        $key_length=strlen($key);
        $string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string;
        $string_length=strlen($string);
        $rndkey=$box=array();
        $result='';
        for($i=0;$i<=255;$i++)
        {
            $rndkey[$i]=ord($key[$i%$key_length]);
            $box[$i]=$i;
        }
        for($j=$i=0;$i<256;$i++)
        {
            $j=($j+$box[$i]+$rndkey[$i])%256;
            $tmp=$box[$i];
            $box[$i]=$box[$j];
            $box[$j]=$tmp;
        }
        for($a=$j=$i=0;$i<$string_length;$i++)
        {
            $a=($a+1)%256;
            $j=($j+$box[$a])%256;
            $tmp=$box[$a];
            $box[$a]=$box[$j];
            $box[$j]=$tmp;
            $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
        }
        if($operation=='D')
        {
            if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8)) // www.jbxue.com
            {
                return substr($result,8);
            }
            else
            {
                return'';
            }
        }
        else
        {
            $rdate = str_replace('=','',base64_encode($result));
            $rdate = str_replace($src,$dist,$rdate);
            return $rdate;
        }
    }
    
    public static function canReply($uid){
        $time=R::get("reply-user-".$uid);
        if(time()-$time<12)return false;
        return true;
    }
    public static function setLastPublish($uid){
        R::setex("publish-user-".$uid,300,time());
    }
    public static function canPublish($uid){
        $time=R::get("publish-user-".$uid);
        if(time()-$time<120)return false;
        return true;
    }
    public static function show($filename,$path="",$params=[],$echo=false){
        if(strstr($filename,".")){
            $data= self::loadRes($filename,$path,$params);
        }else{
            $data= self::loadRes($filename.".html",$path,$params);
        }
        if($echo) echo $data;
        else return $data;
    }
    private static function intval32($num) {
        $num = $num & 0xffffffff;//消掉高32位
        $p = $num>>31; //取第一位 判断是正数还是负数
        if($p==1) { //负数
            $num = $num-1;
            $num = ~$num; //取反 会当成64位取反,算出来的数就去了,所以取反之后 要消掉 高32位
            $num = $num & 0xffffffff;
            return $num * -1;
        } else {
            return $num;
        }
    }
    public static function hashCode($str) {
        $h = 0;
        $off = 0;
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            $h = self::intval32(self::intval32(31 * $h) + ord($str[$off++]));
        }
        return $h;
    }
    //格式化发布说明内容输出
    public static function formatRcontent($content){
        $lines=explode("\n",$content);
        $html="";
        $lastish=true;
        foreach($lines as $k=>$v){
            if($v[0]==' '&&$v[1]==' '){//双空格开头
                if(!$lastish){
                    $html.="<li>".$v."</li>";
                }else{
                    $html.="<ul><li>".$v."</li>";
                }
                $lastish=false;
            }else{
                if($lastish){
                    $html.="<h2>".$v."</h2>";
                }else{
                    $html.="</ul><h2>".$v."</h2>";
                }
                $lastish=true;
            }
        }
        return $html;
    }
    public static function getAppVerHtml($text){
        $d=explode("|",$text);
        $out="";
        foreach($d as $k=>$v){
            if(empty($v))continue;
            $arr['ver']=$v;
            $out.= self::show("appveritem","mods",$arr);
        }
        return $out;
    }
    public static function loadSvg($name,$w=24,$h=24,$color="#000000"){
        $arr['w']=$w;
        $arr['h']=$h;
        $arr['color']=$color;
        return self::loadRes($name.".svg","./svgs",$arr);
    }
    //格式化TagItem的Li列表
    public static function formatLiList($moddownlist){
        $html="";
        foreach($moddownlist as $k=>$v){
            $html.=self::show("liitem","mods",$v);
        }
        return $html;
    }
    //格式化Release的列表
    public static function formatReleaseList($moddownlist){
        $html="";
        foreach($moddownlist as $k=>$v){
            $json=json_encode(['moddown','downTimes',$v['id'],$v['url']]);
            $ydata="https://m.schub.top/com/down?data=".self::encrypt($json);
            $v['url']=$ydata;
            if(strstr($v['appver'],'|')){
             $dr=explode('|',$v['appver']);   
             foreach ($dr as $kd=>$vd){
                 $v['ver'].='<summary style="color:blueviolet;" class="signed-commit-badge signed-commit-badge-medium verified" style="float:right;" title="Commit signature">'.$vd.'</summary>';
            }
            }else{
            $v['ver']='<summary style="color:blueviolet;" class="signed-commit-badge signed-commit-badge-medium verified" style="float:right;" title="Commit signature">'.$v['appver'].'</summary>';                
            }
            $html.=self::show("resitem","mods",$v);
        }
        $arr['reslist']=$html;
        $arr['count']=count($moddownlist);
        return self::show("details","mods",$arr);
    }




}
class Libs{
    public static function getServerDomain(){
        $a="";
        if($_SERVER['SERVER_PORT']==443)$a="https://";
        else $a="http://";
        return $a.$_SERVER['HTTP_HOST'];
    }
}
function isUserOnline($uid){
    global $onlinearr;
    foreach($onlinearr as $k=>$v){
        if(R::get($v)==$uid){
            return ["online",'在线'];
        }
    }
    return ['offline','离线'];
}
function getRate($arrStr){
    $arr=json_decode($arrStr);
    if(!is_array($arr))$arr=[];
    $a=0;
    foreach($arr as $k=>$v){
        $a+=intval($v);
    }
    return sprintf("%0.1f",($a/count($arr)));
}
//获取请求参数
function I($name,$type=0){
    if($type==0)return $_REQUEST[$name];
    else if($type==1)return $_GET[$name];
    else return $_POST[$name];
}
//获取毫秒时间戳
function _microtime(){
    $time=microtime();
    $dot=floatval(explode(' ',$time)[0]);
    return floatval(time())+$dot;
}
//获取/设置session
function S($name,$value=null){
if($value==null)return $_SESSION[$name];
else $_SESSION[$name]=$value;
return $value;
}
function isAWord($str){
    for($i=0;$i<strlen($str);$i++){
        $m=ord($str[$i]);
        if($m<48||($m>57&&$m<65)||($m>90&&$m<97)||$m>122)return false;
    }
    return true;
}
function getSafeStr($str){
    if(is_numeric($str))return $str;
    else if(isAWord($str))return $str;
    else return '';
}
//获取验证信息
function AU(){
    $a=$_SERVER['HTTP_AUTHORIZATION'];
    $b=explode(' ',$a);
    return $b[1];
}

//获得已登录的用户id
function getLoginUser($die=false){//when $die equals to true 
    if(!check_login()){
        if($die){
            header('HTTP/1.1 401 Unauthorized');
            print_r(json_encode(['code'=>401,'msg'=>'登录已过期，请重新登陆']));
            die;    
        }else return 0;
    }
    return S("uid");
}


?>