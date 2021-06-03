<?php
$data=I('data');
using("Crypt");
$json = Crypt::Decode($data);
$cdata=json_decode($json,true);
if(!empty($cdata)){
    M($cdata[0])->where(['id'=>$cdata[2]])->setInc($cdata[1],1);
    header("Location:".$cdata[3]);
}
?>