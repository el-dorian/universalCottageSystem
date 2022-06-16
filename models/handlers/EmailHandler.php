<?php


namespace app\models\handlers;


use app\models\email\Email;
use app\models\email\MailPreferences;

class EmailHandler
{
    public function sendEmail($receiverMail, $receiverName, $title, $text): bool
    {
        $mail = new Email();
        $mail->setFrom(MailPreferences::getInstance()->senderEmail);
        $mail->setAddress(
            MailPreferences::getInstance()->debugSend ? MailPreferences::getInstance()->testEmailAddress : $receiverMail
        );
        $mail->setSubject(urldecode($title));
        $mail->setBody($text);
        $mail->setReceiverName($receiverName);
        $mail->send();
        return true;
    }
}