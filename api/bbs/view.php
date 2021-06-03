<?php
$id=intval(I("id"));
$result=M("bbslist")->where(['id'=>$id])->find();
if($result==false)$result=[];
else M("bbslist")->where(['id'=>$id])->setInc("views",1);
$result['addTime']=TimeFormat($result['addTime']);
PR(200,"Succ",$result);
