<?php
session_start();

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    // Simulate storing the message (you can replace this with a database query or email sending logic)
    $success = true; // Assume success for now

    if ($success) {
        $_SESSION['feedback'] = [
            'type' => 'success',
            'message' => 'Thank you, ' . $name . '! Your message has been sent successfully. We will get back to you shortly.'
        ];
    } else {
        $_SESSION['feedback'] = [
            'type' => 'error',
            'message' => 'An error occurred while sending your message. Please try again later.'
        ];
    }

    // Redirect back to the contact page
    header('Location: contact.php');
    exit();
} else {
    // If accessed directly, redirect to the contact page
    header('Location: contact.php');
    exit();
}
?>
