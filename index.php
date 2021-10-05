<?php
include("./config.php");
$page=intval(I('page'));
$limit=intval(I("limit"));
if(empty($limit))$limit=10;
if($page<=1)$page=1;
$page-=1;//默认减去1
//处理黑名单
if($ip=="171.44.103.220")XModel::error("404");

$auth=GetAuthCode();//获取游戏内社区发送过来的Auth信息
$user=[];
if(I('pl')!="logout"){
    if(empty(S('uid'))&&!empty($auth)){//auth登录
        $user=M('user')->where(['token'=>$auth])->find();
        if($user!=false){
            SS('uid',$user['id']);
        }
    }
    if(!empty(S('uid'))){
        $user=M('user')->where(['id'=>S('uid')])->find();
        $noreadCount=M("message")->where(['uid'=>S('uid'),'noread'=>1])->count();
    }
    //有cookie
    if(C('userid')&&empty(S('uid'))){
        $user=M('user')->where(['token'=>C('userid')])->find();
        if(!empty($user))SS('uid',$user['id']);
    }else if(!empty(S('uid'))&&empty(C('userid'))){
        CS('userid',$user['token'],24*30*3600);
    }
}

//访问记录
$ip=$_SERVER['REMOTE_ADDR'];
$ick = M("onlines")->field('id,times')->where(['ip'=>$ip])->find();
if(empty($ick))M("onlines")->add(['ip'=>$ip,'livetime'=>time(),'uid'=>$user['id'],'times'=>1,'pl'=>I('pl')]);
else M("onlines")->where(['id'=>$ick['id']])->save(['livetime'=>time(),'uid'=>$user['id'],'times'=>$ick['times']+1,'pl'=>I('pl')]);
$livecount=M("onlines")->where(['livetime'=>['egt',time()-300]])->count();

if($user['islock']){
    XModel::Logout();
    XModel::Set("error","该账号已被锁定");
    XModel::Set("ifnologin","true");
    XModel::Set("headicon","");
    XModel::Load('error','index',false);
}
XModel::Set("user",$user);
XModel::Set("prevpage",$page);
XModel::Set("page",$page);
XModel::Set("limit",$limit);
XModel::Set("nextpage",$page+2);
XModel::Set("WEBPATH",WEB_PATH);
XModel::Set("subtitle","主页");
XModel::Set("CDNURL","https://cdn.schub.top");
if($noreadCount>0)$msgnoreadhtml='<a href="'.XModel::Get("WEBPATH").'/user/message"><span class="badge pull-left" style="background:red;">'.$noreadCount.'</span></a>';
XModel::Set("headicon",$msgnoreadhtml.'<a href="'.XModel::Get("WEBPATH").'/user/index">'.XModel::getHeadimg($user['headimg'],$user['nickname']).'</a>');
XModel::Set("iflogin",!empty(S('uid')));
XModel::Set("ifnologin",!XModel::Get("iflogin"));
XModel::Set("logosvg",XModel::loadsvg("logo",24,24,"#ffffff"));
XModel::Set("serverurl",'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
XModel::Set("onlinecnt",$livecount);
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
if(!empty($user['isadmin']))XModel::Set("isAdmin",$user['isadmin']);

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