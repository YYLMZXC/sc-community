<?php
$file=$_FILES['file'];
if($file['error']>0)_print(300,"上传文件出错");
else{
    $data=file_get_contents($file['tmp_name']);
    $size=getimagesize($file['tmp_name']);
    if($size[0]>64&&$size[1]>64)_print(300,'图标的大小不可超过64x64');
    $str=base64_encode($data);
    unlink($file['tmp_name']);
    PR(200,'上传成功',$str);
}