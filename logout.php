<?php

/**
 * 退出
 * */
XModel::Logout();
XModel::Set("error","登出成功");
XModel::Set("ifnologin","true");
XModel::Load('error','index',false);