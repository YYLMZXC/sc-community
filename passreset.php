<?php
$data=I('data');
if(empty($data))XModel::error("此密码重置链接已失效");
else{
    $q=M("user")->where(['pass_token'=>$data])->find();
    if($q==false){
        Xmodel::error("此密码重置链接已失效");
    }else{
        $do=I("do");
        if(empty(I('do'))){
            XModel::SetTitle("账号密码重置");
            Xmodel::Set('data',$data);
            XModel::Load("passreset","user",false);
        }else if($do=='submit'){
            $pass=I('pass');
            if(empty($pass)){
                Xmodel::error("密码不可为空");
            }else if(!XModel::checkPassValid($pass)){
                Xmodel::error("无效的密码");
            }else{
                $passwd=md5($pass);
                M('user')->where(['id'=>$q['id']])->save(['pass_token'=>'','passwd'=>$passwd,'islock'=>0]);
                XModel::success("密码重置成功，请返回重新登录",XModel::GET("WEBPATH")."/login","返回登录");
            }
        }
    }
}