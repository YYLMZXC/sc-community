<?php
using("Mail/Email");
$do=I('do');
if(empty($do)){
    SS('verifyCode',rand(1000,9999));
    XModel::SetTitle("账号密码找回");
    XModel::Load("passfd","user",false);    
}else if($do=='img'){
    using("Graphics/VerifyCode");
    $xximage=new VerifyCode(80,30);
    $xximage->generateCode(S('verifyCode'));
    $xximage->getImage();
}else if($do=="submit"){
    $email=I('email');
    $code=I('code');
    if(empty($email)||empty($code)){
        XModel::error("请输入邮箱或验证码");
    }elseif($code==S('verifyCode')){
        //SS('verifyCode','');
        $q=M("user")->field('id')->where(['email'=>$email])->find();
        if($q==false)XModel::error("此邮箱未绑定任何账号");
        else if(!empty($q['pass_token'])){
            $token=$q['pass_token'];
            M("user")->where(['id'=>$q['id']])->save(['pass_token'=>$token]);
        }else{
            $token=md5(time().rand(10000,99999));
            M("user")->where(['id'=>$q['id']])->save(['pass_token'=>$token]);
        }
        Email::Send($email,"社区密码找回",'<h1>你的密码重置地址是:<a href="https://m.schub.top'.XModel::GET("WEBPATH").'/passreset?data='.$token.'">https://m.schub.top'.XModel::GET("WEBPATH").'/passreset?data='.$token.'</a></h1>');
        XModel::success("密码重置链接已发送至邮箱，请点击密码重置链接进行密码重置","/com","返回首页");        
    }else XModel::error("验证码输入错误");
}
