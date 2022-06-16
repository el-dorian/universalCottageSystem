<?php


namespace app\models\email;

use Yii;
use yii\base\Model;

class Email extends Model
{
    private string $address;
    private string $receiverName;
    private string $subject;
    private string $body;
    private array $attachment;
    private array $embed;
    private array $attachments;

    /**
     * @var string
     */
    private string $from;

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @param string $receiverName
     */
    public function setReceiverName(string $receiverName): void
    {
        $this->receiverName = $receiverName;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    /**
     * @param array $attachment
     */
    public function setAttachment(array $attachment): void
    {
        $this->attachment = $attachment;
    }

    /**
     * @param string $address
     */
    public function setFrom(string $address): void
    {
        $this->from = $address;
    }

    /**
     *
     */
    public function send(): void
    {
        $mail = Yii::$app->mailer->compose()
            ->setFrom([$this->from => MailPreferences::getInstance()->senderName])
            ->setSubject($this->subject)
            ->setTo([$this->address => $this->receiverName]);
        if (!empty($this->attachment)) {
            $mail->attach($this->attachment['url'], ['fileName' => $this->attachment['name']]);
        }
        elseif (!empty($this->attachments)){
            foreach ($this->attachments as $attachment) {
                $mail->attach($attachment['url'], ['fileName' => $attachment['name']]);
            }
        }
        if(!empty($this->embed)){
            $cid = $mail->embed($this->embed['url'],[
                'fileName' => "QR.png",
                'contentType' => 'image/png'
            ]);
        }
        $mail->setHtmlBody($this->body);
        // попробую отправить письмо, в случае ошибки- вызову исключение
        $mail->send();
    }

    public function setAttachments(array $attachmentsArray): void
    {
        $this->attachments = $attachmentsArray;
    }

    public function setEmbed(array $embedItem)
    {
        $this->embed = $embedItem;
    }


}