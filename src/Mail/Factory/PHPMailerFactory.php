<?php

declare(strict_types=1);

namespace Darflen\Framework\Mail\Factory;

use PHPMailer\PHPMailer\PHPMailer;

class PHPMailerFactory
{
    public function createPHPMailer(): PHPMailer
    {
        return new PHPMailer(true);
    }
}
