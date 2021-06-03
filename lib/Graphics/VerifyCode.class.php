<?php
class VerifyCode{
    private $img;
    public function __construct($w,$h){
        $this->img=imagecreate($w,$h);
        imagefill($this->img,0,0,imagecolorallocate($this->img,255,255,255));        
    }
    public function generateCode($check){
        $y1=rand(0,30); $y2=rand(0,30); $y3=rand(0,30); $y4=rand(0,30);
        imageline($this->img,0,$y1,70, $y3,000); imageline($this->img,0,$y2,70, $y4,000);
        $strx=rand(3,15); $stry=rand(2,15); imagestring($this->img,5,$strx,$stry,substr($check,0,1),
        imagecolorallocate($this->img,34,87,100)); $strx+=rand(15,20);
        $stry=rand(2,15); imagestring($this->img,5,$strx,$stry,substr($check,1,1),imagecolorallocate($this->img,781,117,78));
        $strx+=rand(15,20);
        $stry=rand(2,15); imagestring($this->img,5,$strx,$stry,substr($check,2,1),imagecolorallocate($this->img,160,40,40));
        $strx+=rand(15,20);
        $stry=rand(2,15); imagestring($this->img,5,$strx,$stry,substr($check,3,1),imagecolorallocate($this->img,25,55,10));
    }
    public function getImage(){
        Header("Content-type: image/png");
        imagepng($this->img);
    }
}