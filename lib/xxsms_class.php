<?php
/**
 * 
 * 短信验证码发送类
 * 
 * 
 **/
class xxsms{
    private $account_user;
    private $account_pass;
    private $log=false;//日志输入
    public function __constrict($u,$p){
        $this->account_user=$u;
        $this->account_pass=$p;
    }
    public function send_code($mobile,$code){
        $content="验证码：".$code;
        $this->send($mobile,$content);
    }
    public function send($mobile,$content){
        $timestamp=time();
        $sign=md5($this->account_user.$this->account_pass.$timestamp);
        $url="http://47.96.73.192:8888/v2sms.aspx?action=send&userid=518&timestamp=".$timestamp."&mobile=".$mobile."&content=".urlencode($content)."&sign=".$sign;
        $data=file_get_contents($url);
        $data=json_decode($data,true);
        if($this->log){
            if($data['code']!=0){
                $f=fopen("./smslog.txt","a+");
                fputs($f,"[".date("Y-m-d H:i:s")."]send to:".$mobile." failed.reason:".$data['msg']."\n");
                fclose($f);    
            }
        }
        return $data;        
    }
}

?>