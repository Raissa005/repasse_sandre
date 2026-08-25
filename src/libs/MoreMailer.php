<?php

namespace RR\libs;

use Exception;
use RR\model\Mail;
use PHPMailer\PHPMailer\PHPMailer;

class MoreMailer
{
    public static function enviarEmail($email)
    {
        $mailConfig = (new Mail())->getMailInformation();

        try {
            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->Host = $mailConfig->host;
            $mail->Username = $mailConfig->username;
            $mail->Password = $mailConfig->password;

            switch ($mailConfig->security) {
                case 1:
                    $mail->SMTPSecure = "ssl";
                    break;
            }

            $mail->Port = $mailConfig->port;
            $mail->FromName = $mailConfig->name;

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';

            $mail->setFrom($mailConfig->username, $mailConfig->name);
            $mail->addReplyTo($mailConfig->username, $mailConfig->name);

            $mail->addAddress($email->cc[0]->username, $email->cc[0]->name);

            for ($i = 1; $i < count($email->cc); $i++) {
                $mail->addCC($email->cc[$i]->username, $email->cc[$i]->name);
            }

            for ($i = 0; $i < count($email->bcc); $i++) {
                $mail->addBCC($email->bcc[$i]->username, $email->bcc[$i]->name);
            }

            $mail->Subject = $email->subject;

            $mail->MsgHTML($email->message);

            $mail->send();
        } catch (Exception $ex) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
