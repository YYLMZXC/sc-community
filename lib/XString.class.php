<?php
/**
 * 字符串处理类
 * 
 * */
class XString{
    private $str;
    private $len=0;
    public function __construct($str){
        if(!is_string($str)||empty($str)||$str==null||$str=="")return;
        $this->str=$str;
        $this->len=strlen($str);
    }
    public function endWidth($s){
        $tlen=strlen($s);
        if($tlen>$this->len)return false;
        else{
            $tmp=substr($this->str,$this->len-$tlen,$this->len-1);
            if($tmp==$s)return true;
            else return false;
        }
    }
    public function startWidth($s){
        $tlen=strlen($s);
        if($tlen>$this->len)return false;
        else{
            $tmp=substr($this->str,0,$tlen);
            if($tmp==$s)return true;
            else return false;
        }
    }
    public static function FormatSearch($key,$value){
        $kcnt=mb_strlen($key);
        $vcnt=mb_strlen($value);
        $newStr="";
        for($i=0;$i<$vcnt;$i++){
            $lcnt=0;
            for($j=0;$j<$kcnt;$j++){
                $k1=mb_substr($key,$j,1);
                $k2=mb_substr($value,$i+$j,1);
                $f=false;
                if(strlen($k1)==1&&strlen($k2)==1&&strtolower($k1)==strtolower($k2))$f=true;
                if($k1==$k2||$f){
                    $lcnt+=1;//连续数+1
                }else{
                    break;
                }
            }
            if($lcnt>0){
                $newStr.="<font color=\"red\">".mb_substr($value,$i,$lcnt)."</font>";
                $i+=($lcnt-1);
            }else{
                $newStr.=mb_substr($value,$i,1);
            }
        }
        return $newStr;
    }
    
    private static function intval32($num) {
        $num = $num & 0xffffffff;//消掉高32位
        $p = $num>>31; //取第一位 判断是正数还是负数
        if($p==1) { //负数
            $num = $num-1;
            $num = ~$num; //取反 会当成64位取反,算出来的数就去了,所以取反之后 要消掉 高32位
            $num = $num & 0xffffffff;
            return $num * -1;
        } else {
            return $num;
        }
    }    
    public static function hashCode($str) {
        $h = 0;
        $off = 0;
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            $h = self::intval32(self::intval32(31 * $h) + ord($str[$off++]));
        }
        return $h;
    }    
}
