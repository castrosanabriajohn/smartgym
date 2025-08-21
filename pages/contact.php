<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $msg     = trim($_POST['message'] ?? '');

    if (!$name || !$email || !$subject || !$msg) {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email.';
    } else {
        // Guardar en BD (opcional)
        $stmt = $db->prepare('INSERT INTO contact_messages (name, email, subject, message) VALUES (:name, :email, :subject, :message)');
        $stmt->execute([':name'=>$name, ':email'=>$email, ':subject'=>$subject, ':message'=>$msg]);

        $gmailUser        = 'castrosanabriajohn@gmail.com';
        $gmailAppPassword = 'urfuygrfrbonvacl'; // tu App Password de Gmail

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $gmailUser;
            $mail->Password   = $gmailAppPassword;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom($gmailUser, 'SmartGym');
            $mail->addAddress($gmailUser, 'SmartGym Admin');
            $mail->addReplyTo($email, $name);

            $mail->isHTML(true);
            $mail->Subject = 'SMARTGYM - Contacto: ' . $subject;
            $mail->Body    = '<h3>Nuevo mensaje de contacto</h3>'
                           . '<p><strong>Nombre:</strong> ' . htmlspecialchars($name)  . '</p>'
                           . '<p><strong>Email:</strong> '  . htmlspecialchars($email) . '</p>'
                           . '<p><strong>Asunto:</strong> ' . htmlspecialchars($subject) . '</p>'
                           . '<p><strong>Mensaje:</strong><br>' . nl2br(htmlspecialchars($msg)) . '</p>';
            $mail->AltBody = "Nombre: $name\nEmail: $email\nAsunto: $subject\n\n$msg";

            $mail->send();
            $message = 'Your message has been received and emailed. Thank you!';
        } catch (Exception $e) {
            $message = 'Your message was saved. Email could not be sent (check SMTP config).';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="text-center mb-3">Contact Us</h2>
            <p class="text-center text-muted mb-4">Have questions or feedback? We’d love to hear from you.</p>

            <?php if ($message): ?>
                <div class="alert alert-success text-center" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger text-center" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="post" action="" class="card shadow-sm p-4">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="subject" class="form-label">Subject</label>
                    <input type="text" id="subject" name="subject" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Message</label>
                    <textarea id="message" name="message" rows="5" class="form-control" required></textarea>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
