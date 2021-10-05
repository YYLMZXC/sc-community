<?php
if(!XModel::Get("isAdmin"))XModel::error("你还没有权限");
$nickname=$user['nickname'];
XModel::Set('nickname',$nickname);
XModel::Load("index","admin",false);


