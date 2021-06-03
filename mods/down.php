<?php
$id=$urlparams['_p0'];
if(!is_numeric($id))$id=0;
$data=$msql->table('moddown')->where(['id'=>$id])->find();
$user=$msql->table('user')->where(['id'=>$data['uid']])->find();
if($data==false)_print(300,'错误的数据');
else {
    header("cache-control:public,max-age=864000");//缓存控制
    header("pragma:public");//缓存控制
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.substr($data['path'],1,strlen($data['path'])).'"');
    echo file_get_contents('./modsfile/'.$data['url']);
}
$msql->table('moddown')->where(['id'=>$id])->setInc('downTimes',1);
?>