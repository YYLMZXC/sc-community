<?php
//字符串或数组格式化为html
class Formatter{
    public $data;
    public function __construct($data){
        $this->data = $data;
    }
    public function setColor($color){
        return $this->data = "<font color=\"$color\">".$this->data."</font>";
    }
    public function setDiv($className,$id=""){
        return $this->data="<div class=\"$className\" id=\"$id\">".$this->data."</div>";
    }
    
}