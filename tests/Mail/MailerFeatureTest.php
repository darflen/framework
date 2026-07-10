<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Mail;

use Darflen\Framework\Config\Config;
use Darflen\Framework\Filesystem\Factory\FilesystemFactory;
use Darflen\Framework\Mail\Factory\PHPMailerFactory;
use Darflen\Framework\Mail\Mailer;
use Darflen\Framework\Mail\Message;
use Darflen\Framework\View\Template\Directives\CompilesBrackets;
use Darflen\Framework\View\Template\Engine;
use Darflen\Framework\View\View;
use Override;
use PHPUnit\Framework\TestCase;

class MailerFeatureTest extends TestCase
{
    private static Config $config;

    private static View $view;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $config = new Config();
        $config->loadConfigArray('mail', [
            'stmp' => [
                'host' => '127.0.0.1',
                'auth' => false,
                'port' => '1025',
                'security' => '',
                'username' => 'foobar',
                'password' => 'fizzbuzz',
            ],
            'from' => [
                'address' => 'foobar@fizzbuzz.com',
                'name' => 'Darflen',
            ]
        ]);
        self::$config = $config;
        self::$view = new View(new Engine([
            new CompilesBrackets()
        ], (new FilesystemFactory())->createLocalFilesystem()));
    }

    public function testSendMail(): void
    {
        $mailer = new Mailer(new PHPMailerFactory(), self::$view, self::$config);
        $mailer->sendEmail(['text' => __DIR__ . '/Fixtures/qux.txt', 'html' => __DIR__ . '/Fixtures/quux.php'], ['name' => 'FizzBuzz'], function (Message $message) {
            $message->toAddress('success@fizzbuzz.com', 'Success');
            $message->setSubject('Foobar');
        });
        $this->assertTrue(true);
    }
}
