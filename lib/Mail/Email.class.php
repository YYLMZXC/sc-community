<?php
class Email{
    public static function Send($toEmail,$title,$Content){
        usingp("Mail/PHPMailer");
        usingp("Mail/SMTP");
        try{
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->Host = 'smtq.qq.com';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;
            $mail->CharSet = 'UTF-8';
            $mail->FromName = 'SC中文社区';
            $mail->Username = '780505183@qq.com';
            $mail->Password = 'fatoequbvvauhbeec';
            $mail->From = '780505183@qq.com';
            $mail->isHTML(true);
            $mail->Debugoutput="html";
            $mail->addAddress($toEmail);
            $mail->Subject = $title;
            $mail->Body = $Content;
            //$status = $mail->send();
            //if(!$status){
            //    die(var_dump($mail->ErrorInfo));
        //    }
            return true;
        } catch (phpmailerException $e) {   
            echo $e->errorMessage();
        }
    }
}
?>