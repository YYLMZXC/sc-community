<?php

$total=M("donate")->field("sum(money) as total")->find();
$list=M("donate")->field("name,money,time")->order("time","desc")->select();

$html="";
foreach ($list as $k=>$v){
    $html.='<tr><th>['.$v['time'].']</th><th><font color="blue">'.$v['name'].'</font></th><th>赞助<font color="red">'.$v['money'].'</font>元</th></tr>';
}
XModel::Set("total",round($total['total'],2));
XModel::Set("xcontent",$html);
XModel::SetTitle("SC中文社区-赞助列表");
XModel::SetContent(XModel::Load("sponsor","more"));