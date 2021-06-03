<?php
class Replylist extends XModel{
    public static function getReplylistTo($id, $page){
        global $outconfig;
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
    public static function getReplylist($id, $page){
        $replyliststr = "";
        $replyinfo = M('bbsreply')->where(['bbsid' => $id])->order('addTime', 'desc')->limit($page, 10)->select();
        foreach ($replyinfo as $k => $v) {
            $users = M('user')->where(['id' => $v['uid']])->find();
            $replyliststr .= self::formatOneReply($users,$v);
        }
        if (empty($replyliststr)) $replyliststr = "暂无回复，需要你的火力支援";
        return $replyliststr;
    }    
    public static function formatOneReply($userinfo, $replyinfo)
    {
        $onarr = isUserOnline($userinfo['id']);
        $replyhtml="";
        $replytolist=M('bbsreplyto')->field('count(*) as num')->where(['rid'=>$replyinfo['id']])->find();
        if(!empty($replytolist['num']))$replyhtml="<a href=\"/com/bbs/replytolist/".$replyinfo['id']."\">查看".$replytolist['num']."条回复</a>";
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
        return parent::LoadFrom('replytoitem', 'bbs',true, ['nickname' => $userinfo['nickname'], 'addTime' => TimeFormat($replyinfo['addTime']), 'uid' => $userinfo['id'], 'content' =>$content, 'usericon' => parent::getHeadimg($userinfo['headimg'], $userinfo['nickname']), 'online' => $onarr[0], 'onlinetext' => $onarr[1],'bid'=>$replyinfo['bbsid'],'rid'=>$replyinfo['id'],'WEBPATH'=>parent::Get("WEBPATH")]);
    }        
}