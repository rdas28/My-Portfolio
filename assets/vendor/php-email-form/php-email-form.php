<?php
class PHP_Email_Form {
    public $to = '';        // Recipient email
    public $from_name = ''; // Sender's name
    public $from_email = ''; // Sender's email
    public $subject = '';    // Email subject
    public $message = '';    // Email body
    public $headers = '';    // Email headers
    public $smtp = false;    // SMTP settings (optional)
    public $ajax = false;    // AJAX support

    public function add_message($content, $label, $priority = 0) {
        $this->message .= "$label: $content\n";
    }

    public function send() {
        if ($this->smtp) {
            return $this->send_with_smtp();
        } else {
            return $this->send_with_mail();
        }
    }

    private function send_with_mail() {
        $this->headers = "From: {$this->from_name} <{$this->from_email}>\r\n";
        $this->headers .= "Reply-To: {$this->from_email}\r\n";
        $this->headers .= "MIME-Version: 1.0\r\n";
        $this->headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        if (mail($this->to, $this->subject, $this->message, $this->headers)) {
            return json_encode(["status" => "success", "message" => "Email sent successfully!"]);
        } else {
            return json_encode(["status" => "error", "message" => "Failed to send email!"]);
        }
    }

    private function send_with_smtp() {
        require 'PHPMailer/PHPMailerAutoload.php'; // Ensure PHPMailer is installed
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $this->smtp['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $this->smtp['username'];
        $mail->Password = $this->smtp['password'];
        $mail->SMTPSecure = 'tls';
        $mail->Port = $this->smtp['port'];

        $mail->setFrom($this->from_email, $this->from_name);
        $mail->addAddress($this->to);
        $mail->Subject = $this->subject;
        $mail->Body = $this->message;

        if ($mail->send()) {
            return json_encode(["status" => "success", "message" => "Email sent successfully via SMTP!"]);
        } else {
            return json_encode(["status" => "error", "message" => "SMTP Error: " . $mail->ErrorInfo]);
        }
    }
}
?>
