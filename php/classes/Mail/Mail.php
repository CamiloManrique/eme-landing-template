<?php
namespace Classes\Mail;

use Classes\Mail\MailException\MailException;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

class Mail
{
    protected $subject;
    protected $from;
    protected $recipient;
    protected $bcc;
    protected $body;

    public function __construct()
    {

    }

    public function setSubject($subject){
        $this->subject = $subject;
        return $this;
    }

    public function setFrom($from){
        $this->from = $from;
        return $this;
    }

    public function setRecipient($recipient){
        $this->recipient = $recipient;
        return $this;
    }

    public function setBcc(array $bcc){
        $this->bcc = $bcc;
        return $this;
    }

    public function setBody($body){
        $this->body = $body;
        return $this;
    }

    /**
     * @throws MailException
     */
    public function send(){
        $subject = empty($this->subject) ? "" : $this->subject;
        $body = empty($this->body) ? "" : $this->body;
        $from = empty($this->from) ? "example@domain.com": $this->from;

        if(empty($this->recipient)){
            throw new MailException("No recipient set");
        }

        if($_ENV['MAIL_DRIVER'] === 'sendmail'){
            $mail_headers = $this->getSendmailHeaders($from);
            mail($this->recipient, $subject, $body, $mail_headers);
        }
        else {

            if ($_ENV['MAIL_DRIVER'] === 'smtp') {
                $transport = new Swift_SmtpTransport($_ENV['MAIL_HOST'], $_ENV['MAIL_PORT']);
                $transport->setUsername($_ENV['MAIL_USERNAME']);
                $transport->setPassword($_ENV['MAIL_PASSWORD']);
            }
            else{
                throw new MailException("The mail {$_ENV['MAIL_DRIVER']} is not supported");
            }

            $mailer = new Swift_Mailer($transport);
            $message = new Swift_Message($subject);

            if(is_array($from)){
                $message->setFrom(...$from);
            }
            else{
                $message->setFrom($from);
            }

            if(is_array($this->recipient)){
                $message->setTo(...$this->recipient);
            }
            else{
                $message->setTo($this->recipient);
            }


            if(!empty($this->bcc)){
                $message->setBcc($this->bcc);
            }

            $message->setBody($body, 'text/html');
            $mailer->send($message);

        }


    }

    protected function getSendmailHeaders($from){
        $mail_headers = "Content-type: text/html; charset=utf8\r\n";
        $recipient_header = is_array($from) ? "From: {$from[1]} <{$from[0]}>" : "From: $from";
        $mail_headers .= "$recipient_header\r\n";
        if(!empty($this->bcc)){
            $bcc_headers = "Bcc: ";
            foreach ($this->bcc as $bcc){
                $bcc_headers .= is_array($bcc) ? "$bcc[1] <$bcc[0]>" : $bcc;
                $bcc_headers .= ", ";
            }
            $mail_headers .= $bcc_headers;
        }

        $mail_headers = rtrim(rtrim($mail_headers), ",");

        return $mail_headers;
    }
}