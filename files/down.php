<?php
//文件id
$id=intval($urlparams['_p0']);
$data=M('files')->where(['id'=>$id])->find();
$user=M('user')->where(['id'=>$data['uid']])->find();
if($data==false)scbbs::error("文件已被删除或不存在");
else {
    header("cache-control:public,max-age=864000");//缓存控制
    header("pragma:public");//缓存控制
    header('Content-Type: application/octet-stream');
    header('Content-Length: '.filesize($data['path']));
    header('Content-Disposition: attachment; filename="'.$data['fname'].'"');
    echo file_get_contents($data['path']);
}
M('files')->where(['id'=>$id])->setInc('downTimes',1);
?>