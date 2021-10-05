<?php

$regcnt=M("user")->field('count(*) as num')->find();
$filecnt=M("directory")->field('count(*) as num')->find();
$stat=M("stat")->where(['time'=>strtotime(date("Y-m-d"))])->find();
$onlinecount=M("onlines")->where(['uid'=>['gt',0]])->count();
XModel::SetTitle("SC中文社区-更多信息");
$c= "
<div style=\"text-align:center;\">
<p>在线用户:".$onlinecount."人</p>
<p>今日社区浏览数:".$stat['times']."次</p>
<p>今日社区试玩数:".$stat['mtimes']."次</p>
<p>今日社区评分数:".$stat['ftimes']."次</p>
<p>总注册用户数:".$regcnt['num']."个</p>
<p>总文件上传数:".$filecnt['num']."个</p>
<p>IP:".$_SERVER['REMOTE_ADDR']."</p>
<p>UA:".$_SERVER['HTTP_USER_AGENT']."</p>
<h2>【<a href=\"/com/sponsor\">收到的赞助</a>】</h2>
<p><img  src=\"./paywx.png\"></p>
</div>
";
XModel::SetContent($c);