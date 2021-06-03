<?php
include("./config.php");
$page=intval(I('page'));
$limit=intval(I("limit"));
if(empty($limit))$limit=10;
if($page<=1)$page=1;
$page-=1;//默认减去1
$auth=GetAuthCode();//获取游戏内社区发送过来的Auth信息
$user=[];
if(empty(S('uid'))&&!empty($auth)){
    $user=M('user')->where(['token'=>$auth])->find();
    if($user!=false){
        SS('uid',$user['id']);
    }
}else{
    $user=M('user')->where(['id'=>S('uid')])->find();
}
if(!empty(S('uid'))){
    $qmsg=M("message")->field("count(*) as num")->where(['uid'=>S('uid'),'noread'=>1])->find();
    $noreadCount=intval($qmsg['num']);
}
//有cookie
if($_COOKIE['user-id']&&empty(S('uid'))){
    $user=M('user')->where(['token'=>$_COOKIE['user-id']])->find();
    if(!empty($user))SS('uid',$user['id']);
}else if(!empty(S('uid'))&&empty($_COOKIE['user-id']))setcookie('user-id',$user['token'],time()+24*30*3600);

XModel::Set("user",$user);
XModel::Set("prevpage",$page);
XModel::Set("page",$page);
XModel::Set("limit",$limit);
XModel::Set("nextpage",$page+2);
XModel::Set("WEBPATH",WEB_PATH);
XModel::Set("subtitle","主页");
XModel::Set("CDNURL","https://cdn.schub.top");

if($noreadCount>0)XModel::Set("msgnoread","<a href=\"/com/user/message\" style=\"padding: 0px 4px;margin: 12px;\" class=\"message\">".$noreadCount."</a>");
XModel::Set("iflogin",!empty(S('uid')));
XModel::Set("ifnologin",!XModel::Get("iflogin"));
XModel::Set("headicon",XModel::getHeadimg($user['headimg'],$user['nickname']));
XModel::Set("logosvg",XModel::loadsvg("logo",24,24,"#ffffff"));
XModel::Set("serverurl",'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
XModel::Set("onlinecnt",1);
XModel::Set("SiteName","SC中文社区");
XModel::SetTitle(XModel::Get("SiteName"));


$indexList=M("bbslist")->field("id,title,flags")->where(['IsIndex'=>1])->select();
$indexListHtml="";
foreach ($indexList as $k=>$v){
    $flags=json_decode($v['flags'],true);
    $imgsxx="";
    foreach ($flags as $kl=>$vl){
        $srcxx=M("bbsflags")->field('src')->where(['id'=>$vl])->find();
        $imgsxx.=XModel::flagToImg($srcxx['src']);
    }
    $indexListHtml.='<div class="listview lv-bordered lv-lg">'.$imgsxx.'<a href="'.XModel::Get("WEBPATH").'/bbs/'.$v['id'].'">'.$v['title'].'</a></div>';
}
XModel::Set("IndexList",$indexListHtml);


if($user['authority']==2||$user['authority']==3){
    XModel::Set("isAdmin",true);
}
$_p=I('pl');//路径
$urlparams="";//剩余的url参数
if(empty($_p)){//空的参数
    XModel::Index();
}else{//解析路径
    using("XString");
    $_pp=new XString($_p);
    if($_pp->endWidth(".php")){
        XModel::Set('error',"路径非法");
        XModel::Set("subtitle","错误页面");
        XModel::Set("debug",$urlpath);
        XModel::Load("error","index", false);
    }
    $urlparse=explode("/",$_p);$counti=0;$urlpath="";
    foreach($urlparse as $k){
        ++$counti;
        if(empty($k)){//为空
            continue;
        }
        if($counti==count($urlparse)) {
            if(strstr($k,',')){
                $extra=explode(',',$k);
                $urlparams="_p0=".$extra[0]."&_p1=".$extra[1];
            }else if(is_numeric($k)){
                $urlparams="_p0=".$k;
            }else{
                $urlpath.='/'.$k;
            }
        } else $urlpath.='/'.$k;
    }
    $urlparams=parseParams($urlparams);
    if(file_exists(".".$urlpath)){
        $len=strlen($urlpath);
        if($len>4&&strstr($urlpath,'.'))if(substr($urlpath,$len-4,$len)!='.php'){
            using("System/IO/Path");
            using("System/IO/Mime");
            $p=new Path($urlpath);
            $type=$p->getExtension();
            $type=substr($type,1,strlen($type)-1);
            if($type=="css"||$type=="js"||$type=="woff"||$type=="woff2"||$type=="jpg"||$type=="png"){
                header("pragma: public");
                header("cache-control: public, max-age=864000");
            }
            header("Content-Type:".Mime::get($type));
            echo file_get_contents(".".$urlpath);die;
        }
    }
    if(!strstr($urlpath,'.')){
        $urlpath.=".php";
    }
    if(!file_exists(INSTALL_PATH.$urlpath)){
        XModel::error("路径非法");
    }else{
        include_once(INSTALL_PATH.$urlpath);
    }
}
?>