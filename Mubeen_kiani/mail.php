<?php
ob_start();
header('Content-Type: application/json');

define('GMAIL_USER', 'mubeenkiani0126@gmail.com');
define('GMAIL_PASS', 'jknwijwrdolqgbrj');
define('MAIL_TO', 'mubeenkiani0126@gmail.com');

function respond($ok, $msg)
{
    ob_clean();
    echo json_encode(['success' => $ok, 'message' => $msg]);
    exit;
}

$base = __DIR__ . '/PHPMailer/src/';

if (!file_exists($base . 'PHPMailer.php')) {
    respond(false, 'PHPMailer not found at: ' . $base);
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require $base . 'PHPMailer.php';
require $base . 'SMTP.php';
require $base . 'Exception.php';

function clean($val)
{
    return htmlspecialchars(strip_tags(trim($val ?? '')));
}

$type = clean($_POST['form_type'] ?? '');
$name = clean($_POST['name'] ?? '');
$email = clean($_POST['email'] ?? '');
$phone = clean($_POST['phone'] ?? '');
$message = clean($_POST['message'] ?? '');

if ($type === 'contact') {
    $subject = 'Portfolio Contact: ' . clean($_POST['subject'] ?? 'New Message');
    $budget = clean($_POST['budget'] ?? 'Not specified');
    $body = "
        <h2 style='color:#333;'>New Contact Form Submission</h2>
        <table style='border-collapse:collapse;width:100%;font-family:Arial,sans-serif;'>
            <tr><td style='padding:8px;border:1px solid #ddd;font-weight:bold;width:140px;'>Name</td><td style='padding:8px;border:1px solid #ddd;'>$name</td></tr>
            <tr><td style='padding:8px;border:1px solid #ddd;font-weight:bold;'>Email</td><td style='padding:8px;border:1px solid #ddd;'>$email</td></tr>
            <tr><td style='padding:8px;border:1px solid #ddd;font-weight:bold;'>Phone</td><td style='padding:8px;border:1px solid #ddd;'>$phone</td></tr>
            <tr><td style='padding:8px;border:1px solid #ddd;font-weight:bold;'>Budget</td><td style='padding:8px;border:1px solid #ddd;'>$budget</td></tr>
            <tr><td style='padding:8px;border:1px solid #ddd;font-weight:bold;'>Message</td><td style='padding:8px;border:1px solid #ddd;'>$message</td></tr>
        </table>
    ";
} elseif ($type === 'popup') {
    $service = clean($_POST['service'] ?? '');
    $project = clean($_POST['project'] ?? '');
    $subject = 'Portfolio Enquiry: ' . $service;
    $body = "
        <h2 style='color:#333;'>New Project Enquiry (Popup Form)</h2>
        <table style='border-collapse:collapse;width:100%;font-family:Arial,sans-serif;'>
            <tr><td style='padding:8px;border:1px solid #ddd;font-weight:bold;width:140px;'>Name</td><td style='padding:8px;border:1px solid #ddd;'>$name</td></tr>
            <tr><td style='padding:8px;border:1px solid #ddd;font-weight:bold;'>Email</td><td style='padding:8px;border:1px solid #ddd;'>$email</td></tr>
            <tr><td style='padding:8px;border:1px solid #ddd;font-weight:bold;'>Phone</td><td style='padding:8px;border:1px solid #ddd;'>$phone</td></tr>
            <tr><td style='padding:8px;border:1px solid #ddd;font-weight:bold;'>Service</td><td style='padding:8px;border:1px solid #ddd;'>$service</td></tr>
            <tr><td style='padding:8px;border:1px solid #ddd;font-weight:bold;'>Project</td><td style='padding:8px;border:1px solid #ddd;'>$project</td></tr>
            <tr><td style='padding:8px;border:1px solid #ddd;font-weight:bold;'>Message</td><td style='padding:8px;border:1px solid #ddd;'>$message</td></tr>
        </table>
    ";
} elseif ($type === 'newsletter') {
    $subject = 'New Newsletter Subscriber';
    $body = "
        <h2 style='color:#333;'>New Newsletter Subscriber</h2>
        <p style='font-family:Arial,sans-serif;'>Email: <strong>$email</strong></p>
    ";
} else {
    respond(false, 'Invalid form type.');
}

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = GMAIL_USER;
    $mail->Password = GMAIL_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom(GMAIL_USER, 'Mubeen Kiani Portfolio');
    $mail->addAddress(MAIL_TO, 'Mubeen Kiani');

    if (!empty($email) && $type !== 'newsletter') {
        $mail->addReplyTo($email, $name);
    }

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;

    $mail->send();
    respond(true, 'Your message has been sent successfully!');

} catch (Exception $e) {
    respond(false, 'Mail error: ' . $mail->ErrorInfo);
}