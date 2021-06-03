<?php
//error_reporting(0);
header("Access-Control-Allow-Origin: *");
include_once("./lib/Gfunc.class.php");//加载全局库

session_start();//开启session
date_default_timezone_set("Asia/Shanghai");//设置时区

using("XModel");
?>