<?php
class Console{
    public static function WriteLine($text){
        $f=fopen(INSTALL_PATH."/log.txt","a+");
        fputs($f,$text);
        fclose($f);
    }
}