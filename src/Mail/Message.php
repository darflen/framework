<?php

declare(strict_types=1);

namespace Darflen\Framework\Mail;

use PHPMailer\PHPMailer\PHPMailer;

class Message
{
    protected PHPMailer $mailer;
    public function __construct(PHPMailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function fromAddress(string $email, string $name = ''): void
    {
        $this->mailer->setFrom($email, $name);
    }

    public function toAddress(string $email, string $name = ''): void
    {
        $this->mailer->addAddress($email, $name);
    }

    public function addCC(string $email, string $name = ''): void
    {
        $this->mailer->addCC($email, $name);
    }

    public function addBCC(string $email, string $name = ''): void
    {
        $this->mailer->addBCC($email, $name);
    }

    public function setSubject(string $subject): void
    {
        $this->mailer->Subject = $subject;
    }

    public function addAttachment(string $path, ?string $name = ''): void
    {
        $this->mailer->addAttachment($path, $name);
    }
}
