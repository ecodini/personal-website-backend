<?php namespace Holamanola45\Www\Lib\Utils;

use Exception;
use Holamanola45\Www\Lib\Error\InternalServerErrorException;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer {

    public PHPMailer $phpMailer;

    function __construct() {
        // ignore exceptions (i do NOT care)
        $this->phpMailer = new PHPMailer(false);

        $this->phpMailer->isSMTP();
        $this->phpMailer->Host       = $_ENV['EMAIL_HOST_SMTP'];
        $this->phpMailer->SMTPAuth   = true;
        $this->phpMailer->Username   = $_ENV['EMAIL_ADDR'];
        $this->phpMailer->Password   = $_ENV['EMAIL_PASS'];
        $this->phpMailer->SMTPSecure = $_ENV['EMAIL_HOST_SECURITY'];
        $this->phpMailer->Port       = $_ENV['EMAIL_HOST_PORT'];
    }

    public function sendEmail(string $subject, string $body, string $body_alt, string $to) {
        try {
            $this->phpMailer->setFrom($_ENV['EMAIL_ADDR'], $_ENV['EMAIL_NAME']);
            $this->phpMailer->addAddress($to);

            $this->phpMailer->isHTML(true);
            $this->phpMailer->Subject = $subject;
            $this->phpMailer->Body    = $body;
            $this->phpMailer->AltBody = $body_alt;

            $this->phpMailer->send();
        } catch (Exception $e) {
            throw new InternalServerErrorException($e->getMessage());
        }
    }

    public function sendActivationEmail(string $email, string $activation_url) {
        $subject = 'Activate Account';
        $body = 'Activate your HOLAMANOLA45 account: ' . $activation_url;

        $this->sendEmail($subject, $body, $body, $email);
    }
}