<?php
class Bbsitem extends XModel{
    
    public static function View($id){
        //获取帖子信息
        $data = M('bbslist')->where(['id' => $id])->find();
        parent::SetTitle($data['title']);
        if (empty($data)||$data['stat']==0) {
            parent::error('该贴已被删除');
        }
        $cateinfo = M('bbscate')->where(['id' => $data['cid']])->find();
        $parentinfo = M('bbscate')->where(['id' => $cateinfo['parent']])->find();
        //获取楼主信息
        if($data['uid']!=S('uid'))
        $userinfo = M('user')->where(['id' => $data['uid']])->find();
        else $userinfo=parent::Get("user");
        $authflags="";
        if($userinfo['isadmin'])$authflags.=self::getmGroup(100);
        $authflags.=self::getmGroup($userinfo['mgroup']);
        $replycnt = M('bbsreply')->field('count(*) as num')->where(['bbsid'=>$id])->find();
        $replycnt = intval($replycnt['num']);
        if($data['lastupdatetime']>0)$outconfig['lastupdatetime']=TimeFormat($data['lastupdatetime']);
        if(!empty($data['edit_user'])){
            if($data['edit_user']!=$data['uid'])$edituser=M("user")->field('nickname')->where(['id'=>$data['edit_user']])->find();
            else $edituser=['nickname'=>$userinfo['nickname']];
            parent::Set('IsEdit',true);//权限标签
        }
        parent::Set('cid',$data['cid']);//版块ID
        parent::Set('uid',$data['uid']);//发帖用户ID
        parent::Set('bid',$id);//权限标签
        parent::Set('title',$data['title']);//帖子标题
        parent::Set('parentcate',$parentinfo['name']);//权限标签
        parent::Set('nowcate',$cateinfo['name']);//权限标签
        parent::Set('authflags',$authflags);//权限标签
        parent::Set('total',$replycnt);//总回复数
        parent::Set('edituid',$data['edit_user']);//编辑用户ID
        parent::Set('editusername',$edituser['nickname']);//编辑的用户名称
        parent::Set('userFlags',parent::decodeFlags($data['flags']));//用户头衔标签
        parent::Set('usericon',parent::getHeadimg($userinfo['headimg'], $userinfo['nickname']));
        parent::Set('nickname',$userinfo['nickname']);//编辑用户ID
        parent::Set('addTime',TimeFormat($data['addTime']));//创建时间
        parent::Set('lastupdatetime',TimeFormat($data['lastupdatetime']));//编辑时间
        parent::Set('postlist',self::getReplyList($id,parent::Get("page")));//回复列表
        parent::Set('content',$data['content']);
        parent::Set('bads',$data['bads']);
        parent::Set('goods',$data['goods']);
        parent::Set('views',$data['views']);
        parent::Set('replies',$data['replies']);
        if($data['uid']==S('uid')||parent::$Conf['isAdmin']){//根据权限生成对应可操作标签
            parent::Set('isself',true);
            parent::Set('func',"<a href=".parent::Get('WEBPATH')."'/bbs/edit/".$data['id']."'>编辑</a>&emsp;<a href='".parent::Get('WEBPATH')."/bbs/del/".$data['id']."'>删除</a>");
        }else parent::Set('isnself',true);
        $cateid = $data['cid'];//版块ID
        $replyliststr = "";
        parent::Set('description',mb_substr(strip_tags($data['content']),0,20)."...");
        M('bbslist')->where(['id' => $id])->setInc('views', 1);
        parent::Set('data',parent::Load('view','bbs',true));//加载帖子视图
        $container=parent::Load("topic","model",true);//加载子视图
        $replyCount = M('bbsreply')->field('count(*) as num')->where(['bbsid' => $id])->find();
        if(empty($replyCount['num']))$replyCount['num']=0;
        $container.=parent::pageHtml(parent::Get('WEBPATH')."/bbs/".$id,parent::Get("page"),$replyCount['num'],5);
        parent::SetContent($container);
    }
    
    public static function getReplylist($id, $page){
        $replyliststr = "";
        $replyinfo = M('bbsreply')->where(['bbsid' => $id])->order('addTime', 'desc')->limit($page, 5)->select();
        foreach ($replyinfo as $k => $v) {
            $users = M('user')->where(['id' => $v['uid']])->find();
            $replyliststr .= self::formatOneReply($users,$v);
        }
        if (empty($replyliststr)) $replyliststr = "暂无回复，需要你的火力支援";
        return $replyliststr;
    }
    public static function getReplylistTo($id, $page){
        $replyliststr = "";
        $replys=M('bbsreplyto')->field('count(*) as num')->where(['rid' => $id])->find();
        if(empty($replys['num']))$replys['num']=0;
        $outconfig['page']=$page;
        $outconfig['total']=intval($replys['num']);
        $replyinfo = M('bbsreplyto')->where(['rid' => $id])->order('addTime', 'desc')->limit($page, 5)->select();
        foreach ($replyinfo as $k => $v) {
            $users = M('user')->where(['id' => $v['uid']])->find();
            if($v['toid']!=0){
                $rdata=M('bbsreplyto')->where(['id'=>$v['toid']])->find();
                $touser=M('user')->where(['id'=>$rdata['uid']])->find();
                $replyliststr .= self::formatOneReplyto($users,$touser,$rdata['content'], $v);
            }else{
                $replyliststr .= self::formatOneReplyto($users,null,null, $v);
            }
        }
        return $replyliststr;
    }

    public static function formatOneReply($userinfo, $replyinfo)
    {
        $onarr = parent::isUserOnline($userinfo['id']);
        $replyhtml="";
        $replytolist=M('bbsreplyto')->field('count(*) as num')->where(['rid'=>$replyinfo['id']])->find();
        if(!empty($replytolist['num']))$replyhtml="<a href=\"".parent::Get('WEBPATH')."/bbs/replytolist/".$replyinfo['id']."\">查看".$replytolist['num']."条回复</a>";
        return parent::LoadFrom('replyitem', 'bbs', true, ['nickname' => $userinfo['nickname'], 'addTime' => TimeFormat($replyinfo['addTime']), 'uid' => $userinfo['id'], 'content' => $replyinfo['content'], 'usericon' => parent::getHeadimg($userinfo['headimg'], $userinfo['nickname']), 'online' => $onarr[0], 'onlinetext' => $onarr[1],'bid'=>$replyinfo['bbsid'],'rid'=>$replyinfo['id'],'goods'=>$replyinfo['goods'],'bads'=>$replyinfo['bads'],'replytonum'=>$replyhtml,'WEBPATH'=>parent::Get("WEBPATH")]);
    }
    public static function formatOneReplyto($userinfo,$touser,$substr,$replyinfo)
    {
        $onarr = parent::isUserOnline($userinfo['id']);
        if(!empty($touser)){
            $content="> 回复".$touser['nickname'].":".$substr."\n\n".$replyinfo['content'];
        }else{
            $content=$replyinfo['content'];
        }
        return parent::LoadFrom('replytoitem', 'bbs', true, ['nickname' => $userinfo['nickname'], 'addTime' => TimeFormat($replyinfo['addTime']), 'uid' => $userinfo['id'], 'content' =>$content, 'usericon' => parent::getHeadimg($userinfo['headimg'], $userinfo['nickname']), 'online' => $onarr[0], 'onlinetext' => $onarr[1],'bid'=>$replyinfo['bbsid'],'rid'=>$replyinfo['id'],'WEBPATH'=>parent::Get("WEBPATH")]);
    }
}