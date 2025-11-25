<?php

/**
 * Email Helper Functions - PHPMailer Implementation
 */

require_once __DIR__ . '/config.php';

/**
 * Send email using PHPMailer (Gmail SMTP)
 */
function send_smtp_email($to, $subject, $message, $isHTML = true)
{
    // Check if PHPMailer is installed
    $autoload_path = BASE_PATH . '/vendor/autoload.php';
    if (!file_exists($autoload_path)) {
        error_log('PHPMailer not installed. Email cannot be sent.');
        return false;
    }

    require_once $autoload_path;

    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_ENCRYPTION; // 'tls'
        $mail->Port       = SMTP_PORT; // 587

        // Disable SSL verification (for localhost/development only)
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($to);
        $mail->addReplyTo(SMTP_FROM_EMAIL, SMTP_FROM_NAME);

        // Content
        $mail->isHTML($isHTML);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = strip_tags($message);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * Quick send email wrapper
 */
function quick_send_email($to, $subject, $html_message)
{
    return send_smtp_email($to, $subject, $html_message, true);
}
