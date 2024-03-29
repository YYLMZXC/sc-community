<?php

//存档、日志上传

if(empty(S('uid'))){
    _print(300,'请先登录'.$auth);
}
using("Game/Upload");
using("System/IO/Path");
using("System/Console");
using("Game");
//获取用户信息
$user=M('user')->where(['id'=>S('uid')])->find();
$fileinfo=Upload::Arg();//文件参数
$filepath=new Path($fileinfo['path']);

$filecontent=Upload::getPostRaw();//文件内容

if(empty($filecontent))PR(300,"文件不能为空");
$prifix="/www/wwwroot/cdn.aijiajia.xyz/gamefiles/".$user['user'];//后面不带/
$savepath=$prifix.$filepath->path;//储存路径
$savepath2=$prifix.$filepath->getPath();
if($filepath->isDir())if(!is_dir($savepath))mkdir($savepath,0755,true);//是文件夹，创建文件夹

if($filepath->isFile()){//是文件，储存文件
    if(!is_dir($savepath2))mkdir($savepath2,0755,true);
    file_put_contents($savepath,$filecontent);
    $exists=M('directory')->where(['uid'=>S('uid'),'path'=>$filepath->path])->find();
    if($exists==false){
        if($filepath->getSCType()==1){//世界读取文件
            $gamemode=Game::readGameMode($savepath);
            $savemode=Game::toGameMode($gamemode);
        }
        $addinfo=['uid'=>S('uid'),'path'=>$filepath->path,'type'=>$filepath->getSCType(),'size'=>$filepath->isFile()?filesize($savepath):0,'addTime'=>time(),'level'=>"[]",'worldType'=>$savemode];
        $addid=M('directory')->add($addinfo);//添加到数据库里面
        
    }else{
        if($filepath->getSCType()==1){//世界读取文件
            $gamemode=Game::readGameMode($savepath);
            $savemode=Game::toGameMode($gamemode);
        }
        M('directory')->where(['id'=>$exists['id']])->save(['size'=>$filepath->isFile()?filesize($savepath):0,'addTime'=>time(),'worldType'=>$savemode]);                
    }
}
    PR(200,'上传成功');
?>