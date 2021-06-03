<?php

//请求json{'path':'/test/path.scword','short_url':false}
//
//对文件创建链接
if(empty(S('uid'))){
    _print(300,'请先登录'.$auth);
}
$fileinfo=json_decode(getPostRaw(),true);
//获取用户信息
$user=$msql->table('user')->where(['id'=>S('uid')])->find();
$shreid=$msql->table('directory')->where(['uid'=>S('uid'),'path'=>$fileinfo['path']])->find();
echo json_encode(['url'=>"http://scmod.aijiajia.xyz/com/down/".$shreid['id']]);



?>