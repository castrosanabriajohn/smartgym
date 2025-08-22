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
        $stmt = $db->prepare('INSERT INTO contact_messages (name, email, subject, message)
                              VALUES (:name, :email, :subject, :message)');
        $stmt->execute([':name'=>$name, ':email'=>$email, ':subject'=>$subject, ':message'=>$msg]);

        $gmailUser        = 'tucorreo@gmail.com';
        $gmailAppPassword = ''; // <-- https://myaccount.google.com/apppasswords

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
                           . '<p><b>Nombre:</b> ' . htmlspecialchars($name)  . '</p>'
                           . '<p><b>Email:</b> '  . htmlspecialchars($email) . '</p>'
                           . '<p><b>Asunto:</b> ' . htmlspecialchars($subject) . '</p>'
                           . '<p><b>Mensaje:</b><br>' . nl2br(htmlspecialchars($msg)) . '</p>';
            $mail->AltBody = "Nombre: $name\nEmail: $email\nAsunto: $subject\n\n$msg";

            $mail->send();
            $message = 'Your message has been received and emailed. Thank you!';
        } catch (Exception $e) {
            $message = 'Your message was saved. Email could not be sent (check SMTP config).';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';

// --- Bootstrap 5 (CDN) ---
echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">';
echo '<script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>';
?>
<section class="py-5 bg-light">
  <div class="container">
    <div class="text-center mb-4">
      <h1 class="fw-bold display-6">Contact Us</h1>
      <p class="text-muted mb-0">Have questions or feedback? We’d love to hear from you.</p>
    </div>

    <?php if ($message): ?>
      <div class="alert alert-success alert-dismissible fade show mx-auto mb-4" style="max-width: 720px;" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-danger alert-dismissible fade show mx-auto mb-4" style="max-width: 720px;" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <div class="row g-4">
      <!-- Info card -->
      <div class="col-lg-5">
        <div class="card shadow-sm h-100">
          <div class="card-body p-4">
            <h5 class="card-title mb-3">Get in touch</h5>
            <p class="text-muted">We usually reply within 24 hours. You can also reach us through:</p>
            <ul class="list-unstyled small">
              <li class="mb-2 d-flex align-items-center">
                <i class="bi bi-geo-alt text-secondary me-2"></i> San José, Costa Rica
              </li>
              <li class="mb-2 d-flex align-items-center">
                <i class="bi bi-telephone text-secondary me-2"></i> +506 8888-8888
              </li>
              <li class="mb-2 d-flex align-items-center">
                <i class="bi bi-envelope text-secondary me-2"></i> hello@smartgym.com
              </li>
            </ul>
            <div class="ratio ratio-16x9 rounded overflow-hidden border">
              <iframe
                src="https://www.youtube.com/embed/wnHW6o8WMas"
                title="SmartGym Intro" allowfullscreen></iframe>
            </div>
          </div>
        </div>
      </div>

      <!-- Form card -->
      <div class="col-lg-7">
        <div class="card shadow-sm">
          <div class="card-body p-4">
            <h5 class="card-title mb-3">Send a message</h5>
            <form method="post" action="" onsubmit="return setLoading()">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="name" name="name" placeholder="Full name" required
                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                <label for="name">Name</label>
              </div>

              <div class="row g-3">
                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="email" class="form-control" id="email" name="email" placeholder="name@email.com" required
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    <label for="email">Email</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject" required
                           value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>">
                    <label for="subject">Subject</label>
                  </div>
                </div>
              </div>

              <div class="form-floating mt-3">
                <textarea class="form-control" placeholder="Write your message" id="message" name="message" style="height: 140px" required><?php
                  echo htmlspecialchars($_POST['message'] ?? '');
                ?></textarea>
                <label for="message">Message</label>
              </div>

              <div class="d-grid d-sm-flex justify-content-sm-end gap-2 mt-4">
                <button id="sendBtn" type="submit" class="btn btn-primary px-4">
                  <span class="spinner-border spinner-border-sm me-2 d-none" id="btnSpin" role="status" aria-hidden="true"></span>
                  <span id="btnText">Send Message</span>
                </button>
                <button type="reset" class="btn btn-outline-secondary">Clear</button>
              </div>
            </form>
          </div>
        </div>
        <p class="text-muted small mt-2">We’ll never share your email.</p>
      </div>
    </div>
  </div>
</section>

<!-- Bootstrap Icons (para los íconos usados arriba) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<script>
function setLoading(){
  const btn = document.getElementById('sendBtn');
  const spin = document.getElementById('btnSpin');
  const txt = document.getElementById('btnText');
  btn.disabled = true;
  spin.classList.remove('d-none');
  txt.textContent = 'Sending...';
  return true;
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>