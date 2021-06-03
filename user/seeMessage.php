<?php
$mid=intval($urlparams['_p0']);
if($mid==0)XModel::error('不存在的消息');
XModel::readMessage($mid);
