<?php

class XModel{
    public static $Conf=[];
    
    //设置Title
    public static function SetTitle($t){
        self::Set("webTitle",$t);
    }
    public static function Set($k,$v){
        self::$Conf[$k]=$v;
    }
    public static function Get($k){
        return self::$Conf[$k];
    }
    //登出
    public static function Logout(){
        SS("uid","");
        if(isset($_COOKIE["user-id"]))
        {
          setcookie("user-id",'',time()-3600,'/');
        }
    }
        //阅读所有消息
    public static function readAllMessage(){
        M('message')->where(['uid'=>S('uid')])->save(['noread'=>0]);
    }
    //阅读消息
    public static function readMessage($msgid){//阅读信息
        $data=M('message')->where(['id'=>$msgid])->find();
        M('message')->where(['id'=>$msgid])->save(['noread'=>0]);
        if($data['type']==1)header('Location:/com/bbs/'.$data['bid']);
        else if($data['type']==2)header('Location:/com/bbs/replytolist/'.$data['rid']);
    }
    //生成token
    public static function makeToken(){
        return md5(time().rand(10000,99999));
    }
    public static function setPublishToken(){
        SS('publish-token',rand(10000,99999).time());
    }
    public static function getPublishToken(){
        return S('publish-token');
    }
    //标签对应图
    public static function flagToImg($str){
        using("XString");
        $s=new XString($str);
        if ($s->startWidth("fa")) return '<i class="' . $str . '"></i>';
        else if($s->startWidth("http")) return "<img src=\"" . $str . "\">";
        else if($s->startWidth("<svg")) return $str;
        else return "";
    }
    public static function addMessage($uid,$fromuid,$msg,$bid,$rid,$type){
        //type 1对帖回复 2对回复回复
        M('message')->add(['uid'=>$uid,'fromuid'=>$fromuid,'noread'=>1,'msg'=>$msg,'bid'=>$bid,'rid'=>$rid,'type'=>$type,'addTime'=>time()]);
    }    
    //检查用户是否在线
    public static function isUserOnline($uid){
        $onlinearr=[];
        foreach($onlinearr as $k=>$v){
            if(true){
                return ["online",'在线'];
            }
        }
        return ['offline','离线'];
    }    
    //解码标签对应图
    public static function decodeFlags($json){
        $str = "";
        if (is_array($json)) {
        }
        if (is_string($json)) {
        }
        $arr = json_decode($json, true);
        if (empty($arr)) {
            return "";
        }
        $arr = M('bbsflags')->field('src')->where(['id' => ['in', $arr]])->select();
        foreach ($arr as $k => $v) { //根据需要返回图片或者emoji
            $str .= self::flagToImg($v['src']);
        }
        return $str;
    }
    public static function getRate($arrStr){
        $arr=json_decode($arrStr);
        if(empty($arr))return 0.0;
        if(!is_array($arr))$arr=[];
        $a=0;
        foreach($arr as $k=>$v){
            $a+=intval($v);
        }
        return sprintf("%0.1f",($a/count($arr)));
    }
    //获取用户头像
    public static function getHeadimg($headimg, $nickname, $style=""){
        if (!empty($headimg)) return '<img id="usericon" class="user-picture" style="'.$style.'" src="' . $headimg . '" alt="' . $nickname . '" title="' . $nickname . '">';
        else return '<div class="user-icon" style="background-color: #795548;" data-original-title="" title="' . $nickname . '">' . mb_substr($nickname, 0, 1, "utf-8") . '</div>';
    }
    //首页
    public static function Index(){
        $str = "";$pi=1;
        $list = M('bbscate')->where(['status' => 1, 'parent' => 0])->order('_order', 'desc')->select(); //获取一级分类
        foreach ($list as $k => $v) {
            $glist = M('bbscate')->where(['parent' => $v['id']])->order('_order', 'desc')->select();
            foreach ($glist as $kk => $vv) {
                //获取该分类下的帖子数和回复数
                $n = M('bbslist')->field('sum(replies) as replycount,count(*) as listcount')->where(['cid' => $vv['id'],'stat'=>1])->find();
                //获取该分类下的第一个帖子
                $t = M('bbslist')->field('title,id,uid')->where(['cid' => $vv['id'],'stat'=>1])->order('id', 'desc')->limit(0, 1)->find();
                //获取用户信息
                $user = M('user')->field('nickname,headimg')->where(['id' => $t['uid']])->find();
                if ($t != false) {
                    $vv['ifbbs'] = 1;
                    $vv['bid'] = $t['id'];
                    $vv['bbsname'] = $t['title'];
                    $vv['headimg'] = self::getHeadimg($user['headimg'], $user['nickname']);
                }
                $vv['replycount'] = $n['replycount'] == "" ? 0 : $n['replycount'];
                $vv['listcount'] = $n['listcount'];
                $vv['cateflag'] = self::decodeFlags($v['flag']);
                $vv['animationdelay']=0.17+$pi*0.08;
                $vv['WEBPATH']=self::Get("WEBPATH");
                $str .= self::LoadFrom("cateitem", 'index',true, $vv);
                ++$pi;
            }
        }
        if (empty($str)) self::Set('catelist',"还没有任何板块");
        else self::Set('catelist',$str);
        self::SetContent(self::Load("cateview","index"));

    }
    //设置内容主体
    public static function SetContent($v){
        self::Set('container',$v);
        self::Load('container', 'model' ,false);
    }
    //加载svg图标
    public static function loadSvg($name,$w=24,$h=24,$color="#000000"){
        $arr['w']=$w;
        $arr['h']=$h;
        $arr['color']=$color;
        return self::loadRes($name.".svg","./svgs",$arr);
    }    
    //格式化模板
    public static function Load($filename,$path="",$returnTransfer=true){
        if(strstr($filename,".")){
            $data= self::loadRes($filename,$path,self::$Conf);
        }else{
            $data= self::loadRes($filename.".html",$path,self::$Conf);
        }
        if(!$returnTransfer){echo $data;die;}
        else return $data;
    }
    
    public static function LoadFrom($filename,$path="",$returnTransfer=true,$obj){
        if(strstr($filename,".")){
            $data= self::loadRes($filename,$path,$obj);
        }else{
            $data= self::loadRes($filename.".html",$path,$obj);
        }
        if(!$returnTransfer) echo $data;
        else return $data;
    }
    
    //加载资源
    public static function loadRes($file,$path="",$params=[]){
        $type=explode(".",$file)[1];
        if(!empty($path))$path.="/";
        if($type=="css"){
            return file_get_contents(INSTALL_PATH."/res/css/".$path.$file);
        }else if($type=="html"){
            $res= file_get_contents(INSTALL_PATH."/res/html/".$path.$file);
            $res=self::loadRegxAddr($res,$params);
            $res=self::loadRegxVar($res,$params);
            $res=self::loadRegxCon($res,$params);
            return $res;
        }else if($type=="js"){
            return file_get_contents(INSTALL_PATH."/res/js/".$path.$file);
        }else if($type=="svg"){
            $res= file_get_contents($path.$file);
            $res=self::loadRegxAddr($res,$params);
            $res=self::loadRegxVar($res,$params);
            $res=self::loadRegxCon($res,$params);
            return $res;
        }else{
            return file_get_contents(INSTALL_PATH."/res/autojs/".$path.$file);
        }
    }
    //匹配替换链接地址
    public static function loadRegxAddr($res,$params){
        $pat="/\\{loadres=\"(.*?)\"\\}/";//匹配符
        preg_match_all($pat,$res,$loadurl);//全部匹配
        foreach($loadurl[1] as $key => $v){
        //获取加载地址
        $replaceContent="";
        if(empty($v))$replaceContent="";
        else if(!file_exists($v)){$replaceContent="<!--not found $v-->";}
        else $replaceContent=file_get_contents($v);
        $res=preg_replace($pat,$replaceContent,$res,1);
        }
        return $res;
    }
    public static function isempty($str){
        if(is_numeric($str))return false;
        else if(is_array($str))return count($str)==0?true:false;
        else if(is_string($str))return $str=="";
        else if(is_bool($str))return $str==false;
        else return $str==null;
    }
    //匹配变量
    public static function loadRegxVar($res,$params){
        $pat='/\{\$([a-zA-Z]*)(.*?)\}/';//匹配符
        preg_match_all($pat,$res,$outvars);
        foreach($outvars[1] as $k=>$v){
            if(!self::isempty($outvars[2][$k])){//数组变量
                preg_match("/\[([a-zA-Z0-9]*)\]/",$outvars[2][$k],$outvars2);//获取数组里面的内容
                if(!self::isempty($params[$v][$outvars2[1]]))$res=preg_replace($pat,$params[$v][$outvars2[1]],$res,1);
                else $res=preg_replace($pat,'',$res,1);
            }else{//纯变量
                if(!self::isempty($params[$v])){
                    $res=preg_replace($pat,$params[$v],$res,1);
                }
                else{
                    $res=preg_replace($pat,'',$res,1);
                }
            }
        }
        return $res;
    }
    //匹配条件
    public static function loadRegxCon($res,$params){
        $pat='/\{([a-zA-Z]+)\}([\s\S]*?){\/([a-zA-Z]+)}/';//匹配符
        preg_match_all($pat,$res,$outvars);
        foreach($outvars[1] as $k=>$v){
            if(self::isempty($params[$v])){
                    $res=preg_replace($pat,'',$res,1);
            }else{
                if(self::isempty($outvars[2][$k])){//中间内容为空
                    $res=preg_replace($pat,$params[$v],$res,1);
                }else{//不为空
                    $tmp=self::loadRegxAddr($outvars[2][$k],$params);
                    $tmp=self::loadRegxVar($tmp,$params);
                    $res=preg_replace($pat,$tmp,$res,1);
                }
            }
        }
        return $res;
    }
    //格式化权限标签
    public static function getAuthFlags($name,$color="#444444"){
        //开发者 4CAF50
        return '<a href="javascript:;">
        <small class="label group-label inline-block" style="background-color: '.$color.';">'.$name.'</small></a>';
    }    
    //获取权限对于Html标签
    public static function getmGroup($index){
        switch($index){
            case 100:return self::getAuthFlags("管理员");
            case 99:return self::getAuthFlags("开发者","#4CAF50");
            case 98:return self::getAuthFlags("SC玩家");
            case 97:return self::getAuthFlags("管理员");
            case 96:return self::getAuthFlags("管理员");
            case 95:return self::getAuthFlags("管理员");
        }
    }
    //检查邮箱是否有效
    public static function isEmail($email)
    {
        $result = trim($email);//trim方法去除首位的空格
        if (filter_var($result, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }    
    //检查密码是否有效
    public static function checkPassValid($str){
        if(empty($str))return false;
        if(strlen($str)<6)return false;
        return true;
    }    
    //输出错误
    public static function error($msg){
        self::Set('subtitle','出现错误');
        self::Set('error',$msg);
        self::Load("error","index",false);
    }
    //输出正确
    public static function success($tip,$url,$name){
        self::Set('subtitle','操作成功');
        self::Set('url',$url);
        self::Set('name',$name);
        self::Set('tip',$tip);
        self::Load("success","index",false);
    }
    //输出翻页Html
    public static function pageHtml($href,$page,$total,$eNum=10){
        $page+=1;
        if($total<=1)$total=1;
        $total=ceil($total/$eNum);
        $d="disabled";
        $mmback=1;$mmto=$total;$mback=$page-1;$mto=$page+1;
        $mmbackdisable="";$mmtodisable="";$mbackdisable="";$mtodisable="";$pagedisable="";
        if(strstr($href,'?'))$href.="&page=";
        else $href.="?page=";
        if($mback<=1)$mback=1;
        if($mto>=$total)$mto=$total;
        if($total==1){
            $pagedisable=$d;
            $mmtodisable=$d;
            $mtodisable=$d;
            $mmbackdisable=$d;
            $mbackdisable=$d;
        }else if($page==1){
            $mmbackdisable=$d;
            $mbackdisable=$d;
        }else if($page==$total){
            $mmtodisable=$d;
            $mtodisable=$d;
        }
        $html='<ul class="pagination lv-pagination" style="background:#717171;">';
        $html.='<li class="first '.$mmbackdisable.'"><a href="'.$href.'1"><i class="fa fa-fast-backward" style="line-height:inherit"></i></a></li>';
        $html.='<li class="'.$mbackdisable.'"><a href="'.$href.$mback.'" aria-label="Previous"><i class="fa fa-chevron-left" style="line-height:inherit"></i></a></li>';
        $html.='<li class="'.$pagedisable.'" onclick="openpage();" class="page select-page"><a style="width: max-content;padding: 0 .5em;line-height:40px;" href="javascript:;">'.$page.' / '.$total.'</a></li>';
        $html.='<li class='.$mtodisable.'><a href="'.$href.$mto.'" aria-label="Next"><i class="fa fa-chevron-right" style="line-height:inherit"></i></a></li>';
        $html.='<li class="last '.$mmtodisable.'"><a href="'.$href.$total.'"><i class="fa fa-fast-forward" style="line-height:inherit"></i> </a></li>';
        $html.="</ul>";
        $html.='<div id="bootbox" class="bootbox modal fade bootbox-prompt in hidden" tabindex="-1" role="dialog" style="display:block; padding-right: 17px;"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="bootbox-close-button close" data-dismiss="modal" aria-hidden="true">×</button><h4 class="modal-title">输入页码</h4></div><div class="modal-body"><div class="bootbox-body"><form class="bootbox-form"><input class="bootbox-input bootbox-input-text form-control" id="pageNu" autocomplete="off" type="text"></form></div></div><div class="modal-footer"><button data-bb-handler="cancel" type="button" class="btn btn-default">取消</button><button data-bb-handler="confirm" type="button" class="btn btn-primary">确认</button></div></div></div></div><script>function openpage(){
        $("#bootbox").removeClass("hidden");
        $("[data-bb-handler=\'confirm\']").click(function(){
            var page=$("#pageNu").val();
            window.location.href="'.$href.'"+page;
            $("#bootbox").addClass("hidden");
        });
        $("[data-bb-handler=\'cancel\']").click(function(){
            $("#bootbox").addClass("hidden");
        });
    }</script>';
        return $html;
    }

}