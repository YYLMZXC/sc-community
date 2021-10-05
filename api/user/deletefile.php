<?php
using("System/IO/Path");
$id=intval(I('id'));
$info=M('directory')->field("uid,isShow,path")->where(['id'=>$id])->find();
if(empty($info))PR(300,"文件已被删除");
if($user['id']!=$info['uid']&&$user['authority']!=2&&$user['authority']!=3){
    PR(300,'你没有操作权限');
}
$p=new Path($info['path']);
if(empty($info['path'])){
    PR(300,"没有文件信息，不能进行删除");    
}
try{
    if(@unlink($p->getSavePath($user['user'],$p->getFullName()))){
        M('directory')->where(['id'=>$id])->delete();
    }
    M('directory')->where(['id'=>$id])->delete();
    PR(200,"文件删除成功");    
}catch(Exception $e){
    PR(300,"删除失败:".$e);
}
