<?php
$id=intval(I('id'));
$info=M('directory')->field("uid,isShow")->where(['id'=>$id])->find();
if(empty($info))PR(300,"不存在的文件");

if($user['id']!=$info['uid']&&$user['authority']!=2&&$user['authority']!=3){
    PR(300,'你没有操作权限');
}

if($info['isShow']==0){
    M("directory")->where(['id'=>$id])->save(['isShow'=>1]);
    PR(200,'发布成功');
}else{
    M("directory")->where(['id'=>$id])->save(['isShow'=>0]);
    PR(200,'取消发布成功');
}