<?php

namespace App\Http\Controllers;

set_time_limit(90);

use Exception;
use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;

class Mail extends Controller
{
    public $to;
    public $toName;
    public $subject;
    public $body;
    public $attachments = [];

    public function sendMail()
    {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'platinum.nmsservers.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'info@wefixservers.xyz';
            $mail->Password = 'o#VR5]b)~48caDe5';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            // Recipients
            $mail->setFrom('info@wefixservers.xyz', company()->company_name);
            $mail->addAddress($this->to, $this->toName);

            // Handle file attachment
            if (is_array($this->attachments) && count($this->attachments) > 0) {
                foreach ($this->attachments as $filePath => $fileName) {
                    $mail->addAttachment($filePath, $fileName);
                }
            }

            // Content
            $mail->isHTML(true);
            $mail->Subject = $this->subject;
            $mail->Body = $this->body;
            $mail->AltBody = strip_tags($this->body);

            $mail->send();
            return (object)['error' => false, 'message' => "Mail sent successfully"];
        } catch (Exception $e) {
            echo "Mailer Error: {$e->getMessage()}";
            return (object)['error' => true, 'message' => "Error: {$e->getMessage()}"];
        }

        return (object)['error' => true, 'message' => "Error sending email"];
    }
}
