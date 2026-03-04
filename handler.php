<?php

ini_set('display_errors', 0); 
error_reporting(0);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

/* ---------------- reCAPTCHA ---------------- */
$secretKey = "6LflFzosAAAAAN5vhtSJ7rBHMSKDHsDZJBlnhvK7"; 
$captcha   = $_POST['g-recaptcha-response'] ?? '';

if (!$captcha) {
    echo json_encode(["success" => false, "message" => "Please verify captcha"]);
    exit;
}

$ch = curl_init("https://www.google.com/recaptcha/api/siteverify");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'secret' => $secretKey,
    'response' => $captcha
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

if (!$result['success']) {
    echo json_encode(["success" => false, "message" => "Captcha verification failed"]);
    exit;
}

/* ---------------- FORM DATA ---------------- */
$name    = trim($_POST['Name'] ?? '');
$email   = trim($_POST['Email'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$message = trim($_POST['Message'] ?? '');

if ($name === '' || $email === '' || $message === '') {
    echo json_encode(["success" => false, "message" => "Required fields missing"]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "message" => "Invalid email address"]);
    exit;
}

/* ---------------- MAIL ---------------- */
$to = "zeeshan@dhakaan.com"; 
$subject = "New Contact Form Message";

$body =
"You have received a new message:\n\n".
"Name: $name\n".
"Email: $email\n".
"Phone: $phone\n\n".
"Message:\n$message";

// ✅ SAFE FROM ADDRESS (NO SMTP)
$fromEmail = "noreply@localhost";
$fromName  = "Zeeshan Dhakaan Website";

$headers  = "From: $fromName <$fromEmail>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

if (mail($to, $subject, $body, $headers)) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Mail sending failed"]);
}

exit;
