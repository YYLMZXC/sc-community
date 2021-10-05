<?php
/**
 * 用户管理中心
 * 
 * */
if(empty(S('uid'))){
    XModel::error("请先登录");
}

using("System/IO/Path");
using("Html/Formatter");
using("XString");
$do=I('do');
$ext=I('ext');
$lid=I('lid');
$pass=I('pass');

//获取用户信息
XModel::SetTitle("用户中心");

$Formatter=new Formatter($user['user']);
$Formatter2=new Formatter($user['nickname']);
$msglist=M('message')->field('count(*) as num')->where(['uid'=>S('uid'),'noread'=>1])->find();

XModel::Set("flags",XModel::decodeFlags($user['flags']));
XModel::Set("user",$Formatter->setColor('red'));
XModel::Set("money",$user['money']);
XModel::Set("nickname",$Formatter2->setColor('green'));
XModel::Set("lastlogintime",date('Y-m-d H:i:s',$user['last_login_time']));
XModel::Set("accessToken",$user['token']);
XModel::Set("headimg",XModel::getHeadimg($user['headimg'],$user['nickname'],"width:5em;height:5em;"));
XModel::Set("email",$user['email']);
XModel::Set("msgcount",intval($msglist['num']));
if(empty($do)){
    XModel::Load("index","user",false);
}else{
    if($do=="changenickname"){
        XModel::SetTitle("用户中心-昵称修改");
        $nickname=I('nickname');
        if($ext=='submit'){
            $Formatter=new Formatter("修改成功");
            M('user')->where(['id'=>S('uid')])->save(['nickname'=>$nickname]);
            XModel::LoadFrom("changenickname","user",false,['tip'=>$Formatter->setColor('red'),'input'=>htmlspecialchars($nickname)]);
        }
            XModel::LoadFrom("changenickname","user",false,['tip'=>$Formatter->setColor('red'),'input'=>htmlspecialchars($user['nickname'])]);
    }
    else if($do=="changepw"){
        XModel::SetTitle("用户中心-修改密码");
        if(empty($ext)){
            XModel::Load("changepw","user",false);
        }else{
            if($ext=="submit"){
                if(strlen($pass)<6){
                    XModel::LoadFrom("changepw","user",false,['iferr'=>['tip'=>"密码长度不能小于6位","tipcolor"=>"red"]]);die;
                }else{
                    M('user')->where(['id'=>S('uid')])->save(['passwd'=>md5($pass)]);
                    XModel::logout();
                    XModel::LoadFrom("changepw","user",false,['iferr'=>['tip'=>"密码修改成功","tipcolor"=>"green"],'succ'=>true]);die;
                }
            }
        }
    }
    else if($do=="manage"){
        XModel::SetTitle("用户中心-上传文件管理");
        $total=M('directory')->field('count(*) as num')->where(['uid'=>S('uid')])->find()['num'];
        $back="<a href=\"./index?do=manage&page=$page\">返回列表</a>";
        $list=M('directory')->where(['uid'=>S('uid')])->order('addTime','desc')->limit($page,10)->select();
        $output='
                    <table class="table" style="margin-bottom:2em;">
                    <tr>
                        <th>序号</th>
                        <th>类型</th>
                        <th>名称</th>
                        <th>选择操作</th>
                    </tr>          
        ';$i=$page*10+1;
        foreach($list as $k=>$v){
            if($v['isShow']==0){$pbtext="发布";$dn="";}
            elseif($v['isShow']==1){$pbtext="取消发布";$dn="btn-danger";}                    
            $p=new Path($v['path']);
            $output.="<tr><td>$i</td><td><font class=\"bt btn-sm btn-primary\">".Path::GetScTypeText($v['type'])."</font></td><td><a href=\"./index?do=manage&lid=".($v['id'])."&ext=view&page=$page\">".$p->getFileName()."</a> (".ceil(($v['size']/1024))."KB)&emsp;</td><td><a class=\"btn btn-primary btn-sm\" href=\"javascript:;\" onclick='check(".$v['id'].");'>删除</a>&emsp;<a class=\"btn btn-sm $dn\" href=\"javascript:;\" onclick='publish(".$v['id'].");'>$pbtext</a></td></tr>";
            ++$i;
        }
        if($output=="")$output="<p>还没有文件嗷</p>";
        $output.='
        </table>
        <script>
        
            function check(id){
                if(confirm("你确定要删除吗?"))Ajax("/com/api/user/deletefile","id="+id,function (res){
                    layer.msg(res.msg);
                    setTimeout(function(){window.location.reload()},500);
                });
            }
            function publish(id){
                if(confirm("你确定要发布吗?"))Ajax("/com/api/user/publishfile","id="+id,function (res){
                    layer.msg(res.msg);
                    setTimeout(function(){window.location.reload()},500);
                });
            }
        
        </script>               
        ';
        $output.=XModel::pageHtml("./index?do=manage",$page,$total);
        XModel::Set("data",$output);
        XModel::SetContent(XModel::Load("worldsview","user"));
    }
    else if($do=="manageadmin"){
        XModel::SetTitle("管理用户上传文件");
        $q=I("q");
        $total=0;
        $back="<a href=\"./index?do=manageadmin&page=$page\">返回列表</a>";
        if($user['authority']==2||$user['authority']==3){
            if(empty($q)){
                $total=M('directory')->field('count(*) as num')->find()['num'];
                $list=M('directory')->order('addTime','desc')->limit($page,10)->select();
            }else{
                $total=M('directory')->where(['path'=>['like',$q]])->field('count(*) as num')->find()['num'];
                $list=M('directory')->where(['path'=>['like',$q]])->order('addTime','desc')->limit($page,10)->select();
            }
        }else{
            XModel::error("你没有操作权限");
        }
        if(empty($q))$q="请输入关键字";
            $output='
                    <form action="'.XModel::Get("WEBPATH").'/user/index" style="text-align:center;" method="GET">
                        <div style="display:inline-flex;line-height: 27px;width: 80%;">
                            <input type="hidden" name="do" value="manageadmin">
                            <input type="text" name="q" style="width:80%" placeholder="'.$q.'">
                            <input type="submit" style="width:20%;margin:0 0 0 17px;" value="搜索">
                        </div>
                    </form>
                    <table  class="table" style="margin-bottom:2em;">
                    <tr>
                        <th>序号</th>
                        <th>类型</th>
                        <th>用户</th>
                        <th>名称</th>
                        <th>选择操作</th>
                    </tr>                    
                    ';
            $i=$page*10+1;
            foreach($list as $k=>$v){
                if($v['isShow']==0){$pbtext="发布";$dn="";}
                elseif($v['isShow']==1){$pbtext="取消发布";$dn="btn-danger";}
                $p=new Path($v['path']);
                $userinfo=M('user')->where(['id'=>$v['uid']])->find();
                $uname=$userinfo['nickname'];
                if(empty($uname))$uname=$userinfo['user'];
                if(!empty($q)){
                $output.="<tr><td>$i</td><td><font class=\"bt btn-sm btn-primary\">".Path::GetScTypeText($v['type'])."</font></td><td><a href='".XModel::Get("WEBPATH")."/user/".$v['uid']."'><font color='green' style=\"font-size:18px\">$uname</font></a></td><td><a href=\"./index?do=manage&lid=".($v['id'])."&ext=view&page=$page\" style=\"font-size:18px\">".XString::FormatSearch($q,$p->getFileName())."</a> (".ceil(($v['size']/1024))."KB)&emsp;</td><td><a class=\"btn btn-primary btn-sm\" href=\"javascript:;\" onclick='check(".$v['id'].");'>删除</a>&emsp;<a class=\"btn btn-sm $dn\" href=\"javascript:;\" onclick='publish(".$v['id'].");'>$pbtext</a></td></tr>";  
                }else{
                $output.="<tr><td>$i</td><td><font class=\"bt btn-sm btn-primary\">".Path::GetScTypeText($v['type'])."</font></td><td><a href='".XModel::Get("WEBPATH")."/user/".$v['uid']."'><font color='green' style=\"font-size:18px\">$uname</font></a></td><td><a href=\"./index?do=manage&lid=".($v['id'])."&ext=view&page=$page\" style=\"font-size:18px\">".$p->getFileName()."</a> (".ceil(($v['size']/1024))."KB)&emsp;</td><td><a class=\"btn btn-primary btn-sm\" href=\"javascript:;\" onclick='check(".$v['id'].");'>删除</a>&emsp;<a class=\"btn btn-sm $dn\" href=\"javascript:;\" onclick='publish(".$v['id'].");'>$pbtext</a></td></tr>";                        
                }
                ++$i;
            }
            if($output=="")$output="<p>还没有文件嗷</p>";
            $output.='
            </table>
            <script>
            
                function check(id){
                    if(confirm("你确定要删除吗?"))Ajax("/com/api/user/deletefile","id="+id,function (res){
                        layer.msg(res.msg);
                        setTimeout(function(){window.location.reload()},500);
                    });
                }
                function publish(id){
                    if(confirm("你确定要发布吗?"))Ajax("/com/api/user/publishfile","id="+id,function (res){
                        layer.msg(res.msg);
                        setTimeout(function(){window.location.reload()},500);
                    });
                }
            
            </script>
            ';
            $output.=XModel::pageHtml("./index?do=manageadmin",$page,$total);
            XModel::Set('data',$output);
            XModel::SetContent(XModel::Load("worldsview","user"));
        
    }
    else if($do=="changeemail"){
        XModel::Set("email",$user['email']);
        if($ext!='submit'){
            XModel::Load("changeemail","user",false);
        }else{
            $email=I('email');
            if(!XModel::isEmail($email))XModel::error("请输入有效的邮箱");
            else{
                M("user")->where(['id'=>S('uid')])->save(['email'=>$email]);
                XModel::success("修改邮箱成功",XModel::Get("WEBPATH")."/user/index","返回用户中心");
            }
        }
    }
    else XModel::LoadFrom("index","user",false,['SC中国社区-用户中心',$Formatter->setColor('red'),date('Y-m-d H:i:s',$user['last_login_time']),$Formatter2->setColor('green'),$user['token']]);
}

?>