<?php
class XXEmail{
    public static function Send($toEmail,$title,$Content){
        require_once("./lib/mail/SMTP.php");
        require_once("./lib/mail/PHPMailer.php");
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->Host = 'smtp.qq.com';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        $mail->CharSet = 'UTF-8';
        $mail->FromName = 'SC中文社区';
        $mail->Username = '780505183@qq.com';
        $mail->Password = 'wkijiqbfzubqbeai';
        $mail->From = '780505183@qq.com';
        $mail->isHTML(true);
        $mail->addAddress($toEmail);
        $mail->Subject = $title;
        $mail->Body = $Content;
        $status = $mail->send();        
    }
}
?>