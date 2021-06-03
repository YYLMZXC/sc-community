<?php

/**
 * 各种方法封装库
 * Powered By xxq
 * <Date:2021-03-12></Date:2021-03-12>
 * 
**/

/**
 * 数据库信息配置
 * */
define("DB_SRC","localhost");
define("DB_USER","sccomminuty");
define("DB_PASS","MGpG3XA2RPmzLRb7");
define("DB_NAME","sccomminuty");
define("DB_PREFIX","share");
define("WEB_PATH","/com");
define("INSTALL_PATH",$_SERVER['DOCUMENT_ROOT'].WEB_PATH);


//实例化数据库操作
function M($table,$IsSetPrefix=true){
    using("Msql");
    $msql=new Msql(DB_SRC,DB_USER,DB_PASS,DB_NAME);
    if($IsSetPrefix)$msql->setPrefix(DB_PREFIX);
    return $msql->table($table);
}
//获取请求参数
function I($n){
    return $_REQUEST[$n];
}
//获取SESSION信息
function S($n){
    return $_SESSION[$n];
}
function Debug($arr){
    die(print_r($arr));
}

//设置SESSION
function SS($n,$v){
    $_SESSION[$n]=$v;
}
function UEPR($state,$url="",$title=""){
    if($state)$str="SUCCESS";
    else $str="FAIL";
    die(json_encode(['state'=>"SUCCESS",'url'=>$url,"title"=>$title,'original'=>$title]));
}

function TimeFormat($timestamp)
{
    $h = date('H', $timestamp);
    if ($h < 18 && $h >= 12) $state = "下午";
    else if ($h >= 0 && $h < 6) $state = "凌晨";
    else if ($h >= 6 && $h < 12) $state = "上午";
    else if ($h >= 18 && $h < 24) $state = "晚上";
    return date("Y年m月d日 $state H:i", $timestamp);
}
//获取Auth
function GetAuthCode(){
    $a=$_SERVER['HTTP_AUTHORIZATION'];
    $b=explode(' ',$a);
    return $b[1];
}

//获取客户端IP
function GetClientIp() {
    static $realip;
    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $realip = $_SERVER['REMOTE_ADDR'];
        }
    } else {
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('HTTP_CLIENT_IP')) {
            $realip = getenv('HTTP_CLIENT_IP');
        } else {
            $realip = getenv('REMOTE_ADDR');
        }
    }
    return $realip;
}
//解析GET参数
function parseParams($get){
    $data=[];
    if(empty($get))return [];
    if(strstr($get,'=')&&!strstr($get,',')){//传统方式解析
        if(strstr($get,'&')){//多组数据
            $arr=explode("&",$get);
            foreach($arr as $k){
                if(empty($k))continue;
                $line=explode('=',$k);
                $data[$line[0]]=$line[1];
            }
        }else{
            $line=explode("=",$get);
            $data[$line[0]]=$line[1];
        }
    }else if(strstr($get,',')&&!strstr($get,'=')){
        $arr=explode(',',$get);
        foreach($arr as $k){
            if(empty($k))continue;
            $data[]=$k;
        }
    }else return [];
    return $data;
}
//引用文件
function using($p){
    $fname=INSTALL_PATH."/lib/$p.class.php";
    if(file_exists($fname))require_once($fname);
    else die("the reference '$p' does not exist");
}

function usingp($p){
    $fname=INSTALL_PATH."/lib/$p.php";
    if(file_exists($fname))require_once($fname);
    else die("the reference '$p' does not exist");
}

//输出数据
function PR($code, $msg = '操作成功', $data = [], $die = true)
{
    if ($code == 300 && $msg == '操作成功') $msg = '操作失败';
    $return = ['code' => $code, 'msg' => $msg, 'data' => $data];
    if (!empty($data['_extra'])) {
        $tmp = $data['_extra'];
        unset($return['data']['_extra']);
        foreach ($tmp as $key => $v) {
            $return = array_merge($return, [$key => $v]);
        }
    }
    print_r(json_encode($return));
    if ($die) die;
}
