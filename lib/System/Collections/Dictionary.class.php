<?php
class ArrayList{
    public $arr;
    public function __construct($arr){
        if(empty($arr))$this->arr=[];
        else if(is_string($arr))$this->arr=json_decode($arr,true);
        else $this->arr=$arr;
    }
    public function Contains($arr){
        if(empty($arr))return false;
        else if(is_array($arr)){
            
        }else{
            foreach($this->arr as $k=>$v){
                if($v==$arr)return true;
            }
        }
        return false;
    }
    public function Remove($arr){
        if(empty($arr))return false;
        else if(is_array($arr)){
            
        }else{
            foreach($this->arr as $k=>$v){
                if($v==$arr)unset($this->arr[$k]);
            }
        }
        return false;
    }
    public function getArr(){
        return $this->arr;
    }
}
