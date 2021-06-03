<?php
using("model/Bbslist");

$id=$urlparams['_p0'];
$type=I('type');
if(empty($id)){
    XModel::Index();
}else{
    XModel::Set("cid",$id);
    $cdata=M("bbscate")->field('name,parent')->where(['id'=>$id])->find();
    $pdata=M("bbscate")->field('name')->where(['id'=>$cdata['parent']])->find();
    Bbslist::getCateView($id,$type,$page);
}
?>