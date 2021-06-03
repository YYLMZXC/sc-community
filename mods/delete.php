<?php
if(empty(S('uid')))XModel::error("请先登录");
$type=intval(I("type"));
$gid=intval($urlparams['_p0']);
if($type==0)XModel::error('参数错误');
else if($type==1){//删除整个Mods
    $modid=$gid;
    $check=M('modrelease')->where(['modid'=>$modid])->find();
    if(empty($check))XModel::error("已删除或不存在的记录");
    //获得文件并删除
    $downlist=M("moddown")->where(['modid'=>$modid])->select();
    $dnum=M("moddown")->field('count(*) as num')->where(['modid'=>$modid,'uid'=>S('uid')])->find();
    $dnum=intval($dnum['num']);
    foreach($downlist as $k=>$v){
        if($v['uid']!=S('uid'))XModel::error('你没有操作权限');
        $url=explode("/",$v['url']);
        $did=intval($url[count($url)-1]);
        $dinfo=M('files')->where(['id'=>$did])->find();
        if($dinfo!=false){
            unlink($dinfo['path']);//删除文件
            M('files')->where(['id'=>$did])->delete();//删除文件记录
        }
        M('moddown')->where(['id'=>$v['id']])->delete();//删除文件记录
    }
    M('modrelease')->where(['modid'=>$modid])->delete();//删除更新日志记录
    M('modlist')->where(['id'=>$modid])->delete();//删除mod主记录
    XModel::success("删除成功","/com/mods/list","返回我的Mods列表");
}else if($type==2){//删除一条更新日志
    $rid=$gid;
    $modid=0;
    $downlist=M("moddown")->where(['descid'=>$rid])->select();
    foreach($downlist as $k=>$v){
        if($v['uid']!=S('uid'))XModel::error('你没有操作权限');
        $url=explode("/",$v['url']);
        $did=intval($url[count($url)-1]);
        $dinfo=M('files')->where(['id'=>$did])->find();
        if($dinfo!=false){
            unlink($dinfo['path']);//删除文件
            M('files')->where(['id'=>$did])->delete();//删除文件记录
        }
        M('moddown')->where(['id'=>$v['id']])->delete();//删除文件记录
    }
    $tt=M("modrelease")->where(['id'=>$rid])->find();//删除更新日志
    $modid=$tt['modid'];
    if($tt['uid']!=S('uid'))XModel::error('你没有操作权限');
    M("modrelease")->where(['id'=>$rid])->delete();//删除更新日志
    XModel::success("删除成功","/com/mods/desclist/{$modid}","返回更新日志列表");
}else if($type==3){//删除更新日志的文件
    $did=$gid;
    $downlist=M("moddown")->where(['id'=>$did])->find();
    $descid=$downlist['descid'];
    if($downlist==false)XModel::error("已删除或不存在的记录");
    else if($downlist['uid']!=S('uid'))XModel::error('你没有操作权限');
    else{
        $dpath=$downlist['url'];
        $dpath="/www/wwwroot/cdn.aijiajia.xyz/".$dpath;
        if(unlink($dpath)){//删除文件
            $q=M('moddown')->where(['id'=>$did])->delete();//删除文件记录
        }else{
            XModel::error('删除文件失败');
        }
    }
    XModel::success("删除成功","/com/mods/filelist/{$descid}","返回更新日志列表");
}else{
    XModel::error('参数错误');
}