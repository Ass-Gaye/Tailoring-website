<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize inputs
    $name = htmlspecialchars(trim($_POST["name"] ?? ''));
    $email = filter_var(trim($_POST["email"] ?? ''), FILTER_VALIDATE_EMAIL);
    $subject = htmlspecialchars(trim($_POST["subject"] ?? ''));
    $message = htmlspecialchars(trim($_POST["message"] ?? ''));

    // Validate email
    if (!$email) {
        echo "<span style='color:red;'>Invalid email address.</span>";
        exit;
    }

    // Prevent Email Header Injection
    if (preg_match("/[\r\n]/", $email) || preg_match("/[\r\n]/", $name)) {
        echo "<span style='color:red;'>Invalid input detected.</span>";
        exit;
    }

    // Check for empty fields
    if (empty($name) || empty($subject) || empty($message)) {
        echo "<span style='color:red;'>All fields are required.</span>";
        exit;
    }

    // Recipient
    $to = "sohnas491@gmail.com"; // Your email address

    // Email Subject
    $mail_subject = "Contact Form: " . $subject;

    // Create a boundary string (for multipart email)
    $boundary = md5(uniqid(time()));

    // Plain text version
    $plain_text = "Name: $name\nEmail: $email\n\nMessage:\n$message";

    // HTML version
    $html_content = "
    <html>
    <head>
        <title>Contact Form Submission</title>
    </head>
    <body style='font-family: Arial, sans-serif; color: #333;'>
        <h2 style='color: #4CAF50;'>You have a new message from your website contact form</h2>
        <p><strong>Name:</strong> {$name}</p>
        <p><strong>Email:</strong> {$email}</p>
        <p><strong>Message:</strong></p>
        <p style='background:#f9f9f9; padding:10px; border-left:4px solid #4CAF50;'>{$message}</p>
    </body>
    </html>
    ";

    // Headers for multipart/alternative email
    $headers  = "From: Website Contact Form <noreply@yourdomain.com>\r\n";
    $headers .= "Reply-To: {$email}\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Combine plain text and HTML into a single email
    $body  = "--{$boundary}\r\n";
    $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $body .= $plain_text . "\r\n\r\n";

    $body .= "--{$boundary}\r\n";
    $body .= "Content-Type: text/html; charset=UTF-8\r\n";
    $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $body .= $html_content . "\r\n\r\n";
    $body .= "--{$boundary}--";

    // Send the email
    if (mail($to, $mail_subject, $body, $headers)) {
        echo "<span style='color:green;'>Message sent successfully.</span>";
    } else {
        echo "<span style='color:red;'>Failed to send message. Try again later.</span>";
    }
}
?>
