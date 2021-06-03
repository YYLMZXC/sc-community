<?php
if(!S('uid')){
    _print(300,'请先登录');
}else{
    using("System/IO/Upload");
    $up=new Upload();
    $up->setHost("https://cdn.schub.top");
    $up->setNeedDelPath("/www/wwwroot/cdn.aijiajia.xyz/");
    $res=$up->save($_FILES['file'],"/www/wwwroot/cdn.aijiajia.xyz/userimg");
    if($res['code']>0)PR(300,$res['msg']);
    else{
        M('user')->where(['id'=>S('uid')])->save(['headimg'=>$res['downUrl']]);
        PR(200,'上传成功',$res['downUrl']);
    }
}

