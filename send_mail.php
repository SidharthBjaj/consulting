<?php
// send_mail.php
// Simple PHP endpoint to receive contact form POSTs and send an email.
// IMPORTANT: On a production server you should sanitize input, rate-limit, and use a proper mail service (SMTP with authentication) to avoid spam and ensure deliverability.

header('Content-Type: application/json; charset=utf-8');

// Replace this with your real recipient email address before deploying
$recipient = 'bajajsb4@gmail.com';

// Read POST data
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$project = isset($_POST['project']) ? trim($_POST['project']) : '';
$subject = isset($_POST['subject']) ? trim($_POST['subject']) : 'New Contact Message';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

$errors = [];

// Basic validation
if (empty($name)) {
    $errors[] = 'Name is required.';
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'A valid email is required.';
}
if (empty($message)) {
    $errors[] = 'Message is required.';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// Build email
$email_subject = "Website Contact: " . $subject;
$email_body = "You have received a new message from your website contact form.\n\n";
$email_body .= "Name: $name\n";
$email_body .= "Email: $email\n";
$email_body .= "Phone: $phone\n";
$email_body .= "Project: $project\n\n";
$email_body .= "Message:\n$message\n";

$headers = "From: $name <$email>\r\n";
$headers .= "Reply-To: $email\r\n";

// Attempt to send
$sent = false;
try {
    // Using PHP's mail(). On many hosts this works; on others you must configure SMTP or use a 3rd-party API.
    $sent = mail($recipient, $email_subject, $email_body, $headers);
} catch (Exception $e) {
    $sent = false;
}

if ($sent) {
    echo json_encode(['success' => true, 'message' => 'Message sent successfully.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'errors' => ['Failed to send email. Configure SMTP or use an external mail service.']]);
}

?>