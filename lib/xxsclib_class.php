<?php
/**
 * 
 * sc登陆库
 **/
function login($user,$pass){
    $data=['identification'=>$user,'password'=>$pass,'remember'=>true];
    echo Query('https://bbs.yuanjiesf.com/login',$data);
}
function makeToken(){
   return md5(time()."scpbox");
}

?>