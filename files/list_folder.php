<?php
//请求json{'path':'','rescursive':false}
//个人中心的存档列表
using("System/IO/Path");
$data=M('directory')->where(['uid'=>S('uid')])->order('addTime','desc')->limit($page*15,15)->select();
$entries=[];
foreach($data as $k=>$v){
    $p=new Path($v['path']);
    $entries[]=['path_display'=>$p->getFullName(),'.tag'=>$p->TypeToSC($v['type']),'server_modified'=>date('Y-m-d H:i:s',$v['addTime']),'size'=>intval($v['size'])];
}
$data=['entries'=>$entries];
echo json_encode($data);

?>