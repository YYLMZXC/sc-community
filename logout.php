<?php

/**
 * 退出
 * */
 $_SESSION=array();
XModel::Logout();
XModel::Set("error","登出成功");
XModel::Set("ifnologin","true");
XModel::Set("headicon","");
XModel::Load('error','index',false);
