<?php
$id=intval(I("id"));
$result=M("user")->field("nickname,headimg,last_login_time,mgroup,regtime,isadmin,money")->where(['id'=>$id])->find();
if($result==false)$result=[];
PR(200,"Succ",$result);