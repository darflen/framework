<?php

declare(strict_types=1);

namespace Darflen\Framework\Mail;

use Darflen\Framework\Config\Config;
use Darflen\Framework\Mail\Factory\PHPMailerFactory;
use Darflen\Framework\View\View;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    private Config $config;

    private PHPMailer $mailer;

    private View $view;

    public function __construct(PHPMailerFactory $mailer, View $view, Config $config)
    {
        $mailer = $mailer->createPHPMailer();
        $mailer->isSMTP();
        $mailer->Host = $config->get('mail.stmp.host');
        $mailer->SMTPAuth = $config->get('mail.stmp.auth', false);
        if ($mailer->SMTPAuth) {
            $mailer->Username = $config->get('mail.stmp.username');
            $mailer->Password = $config->get('mail.stmp.password');
        }
        $mailer->SMTPSecure = $config->get('mail.stmp.security');
        $mailer->Port = $config->get('mail.stmp.port');
        $mailer->setFrom($config->get('mail.from.address'), $config->get('mail.from.name'));
        $this->config = $config;
        $this->mailer = $mailer;
        $this->view = $view;
    }

    protected function parseTemplates(string|array $template, array $data): array
    {
        $results = [];
        if (is_array($template) && (isset($template['html']) || isset($template[0]))) {
            $results['html'] = $this->view->viewTemplate($template['html'], $data, true);
        }
        if (is_array($template) && (isset($template['text']) || isset($template[1]))) {
            $results['text'] = $this->view->viewTemplate($template['text'], $data, true);
        }
        if (is_string($template)) {
            $results['html'] = $this->view->viewTemplate($template, $data, true);
        }
        return $results;
    }

    public function sendEmail(string|array $template, array $data, callable $callable): void
    {
        $message = new Message($this->mailer);
        call_user_func($callable, $message);
        $templates = $this->parseTemplates($template, $data);
        $htmlBody = $templates['html'] ?? '';
        $textBody = $templates['text'] ?? '';
        $this->mailer->isHTML(true);
        $this->mailer->Body = $htmlBody;
        if (!empty($textBody)) {
            $this->mailer->AltBody = $textBody;
        }
        $this->mailer->send();
    }
}
