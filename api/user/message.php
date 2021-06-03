<?php
if(empty(S('uid')))_print(300,'请先登录');
XModel::readAllMessage();
PR(200,'操作成功');