<?php
$id=intval(I("id"));
$bid=intval(I("bid"));
$result=M("bbsreplyto")->where(['bid'=>$bid,'rid'=>$id])->limit($page,$limit)->select();
if($result==false)$result=[];
PR(200,"Succ",$result);