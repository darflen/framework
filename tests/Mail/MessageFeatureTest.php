<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Mail;

use Darflen\Framework\Mail\Message;
use PHPMailer\PHPMailer\PHPMailer;
use PHPUnit\Framework\TestCase;

class MessageFeatureTest extends TestCase
{
    public function testSetters(): void
    {
        $mailer = new PHPMailer(true);
        $message = new Message($mailer);

        $message->fromAddress('fizzbuzz@foobar.com', 'foobar');
        $message->toAddress('foobar@fizzbuzz.com', 'fizzbuzz');
        $message->addCC('foo@fizzbuzz.com', 'foo');
        $message->addBCC('bar@fizzbuzz.com', 'bar');
        $message->addAttachment(__DIR__ . '/Fixtures/qux.txt', 'quux.txt');
        $message->setSubject('Hello, World!');

        $this->assertArrayHasKey(0, $mailer->getAttachments());
        $this->assertIsArray($mailer->getAttachments()[0]);
        $this->assertSame([['foo@fizzbuzz.com', 'foo']], $mailer->getCcAddresses());
        $this->assertSame([['bar@fizzbuzz.com', 'bar']], $mailer->getBccAddresses());
        $this->assertSame([['foobar@fizzbuzz.com', 'fizzbuzz']], $mailer->getToAddresses());
        $this->assertSame('fizzbuzz@foobar.com', $mailer->From);
        $this->assertSame('foobar', $mailer->FromName);
        $this->assertSame('Hello, World!', $mailer->Subject);
    }
}
