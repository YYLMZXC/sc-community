<?php
$id=intval(I("id"));
$result=M("bbsreply")->where(['bbsid'=>$id])->limit($page,$limit)->select();
if($result==false)$result=[];
PR(200,"Succ",$result);