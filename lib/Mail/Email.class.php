<?php
class Email{
    public static function Send($toEmail,$title,$Content){
        return "";
        usingp("Mail/PHPMailer");
        usingp("Mail/SMTP");
        try{
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;
            $mail->CharSet = 'UTF-8';
            $mail->FromName = 'SC中文社区';
            $mail->Username = 'xiaoxuanaixiaojia@gmail.com';
            $mail->Password = 'wangjiashi0514';
            $mail->From = 'xiaoxuanaixiaojia@gmail.com';
            $mail->isHTML(true);
            $mail->Debugoutput="html";
            $mail->addAddress($toEmail);
            $mail->Subject = $title;
            $mail->Body = $Content;
            $status = $mail->send();
            if(!$status){
                die(var_dump($mail->ErrorInfo));
            }else{
                echo "Success";
            }
            return true;
        } catch (phpmailerException $e) {   
            echo $e->errorMessage();
        }
    }
}
?>