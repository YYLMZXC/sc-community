<?php
if(!S('uid')){
    _print(300,'请先登录');
}else{
    using("System/IO/Upload");
    $up=new Upload();
    $up->setHost("https://cdn.schub.top");
    $up->setNeedDelPath("/www/wwwroot/cdn.aijiajia.xyz/");
    $up->setAcceptType(['png','jpg','gif','jpeg','bmp']);
    $res=$up->save($_FILES['file'],"/www/wwwroot/cdn.aijiajia.xyz/bbsfiles");
    if($res['code']>0)die(json_encode(['code'=>300,'message'=>$res['msg'],'url'=>'']));
    else{
        die(json_encode(['code'=>200,'message'=>'上传成功','url'=>$res['downUrl'],'size'=>$res['rsize']]));        
    }
}

