<?php
// index.php
require_once __DIR__ . '/includes/header.php';

$stmt = $db->prepare('SELECT c.id, c.title, c.start_at, c.end_at, c.capacity, c.trainer, a.name AS activity_name, a.description AS activity_description FROM classes c JOIN activity_types a ON c.activity_type_id = a.id ORDER BY c.start_at ASC LIMIT 3');
$stmt->execute();
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->query('SELECT * FROM trainers');
$trainers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->query('SELECT * FROM membership_plans');
$memberships = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<section id="home" class="hero-gradient text-white py-5">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-6 mb-4 mb-md-0 fade-in">
        <h1 class="display-4 fw-bold mb-4">BUILD YOUR <span class="text-primary">PERFECT</span> BODY</h1>
        <p class="lead text-light mb-4">Join our premium fitness community and transform your body with our state-of-the-art facilities and expert trainers.</p>
        <div class="d-flex gap-3">
          <a href="/smartgym/pages/register.php" class="btn btn-primary btn-lg">Join Now</a>
          <a href="#classes" class="btn btn-outline-light btn-lg">Our Classes</a>
        </div>
      </div>
      <div class="col-md-6 fade-in delay-1">
        <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=1470&q=80" alt="Gym Hero Image" class="img-fluid rounded shadow-lg">
      </div>
    </div>
  </div>
</section>

<section class="py-5 bg-white">
  <div class="container">
    <div class="text-center mb-5 fade-in">
      <h2 class="fw-bold display-6 mb-3">YOUR <span class="text-primary">DIGITAL FITNESS PLATFORM</span></h2>
      <div class="mx-auto mb-4" style="width:80px;height:4px;background-color:#0d6efd;"></div>
      <p class="text-muted mx-auto" style="max-width:600px;">Access all your fitness tools in one place with our integrated platform</p>
    </div>
    <div class="row g-4">
      <div class="col-md-4 fade-in">
        <div class="card h-100 text-center shadow-sm border-0">
          <div class="card-body">
            <div class="text-primary display-4 mb-3"><i class="fas fa-user-cog"></i></div>
            <h3 class="h5 fw-bold mb-3">Personalized Profiles</h3>
            <p class="text-muted">Track your progress, set goals, and get customized workout plans based on your objectives.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4 fade-in delay-1">
        <div class="card h-100 text-center shadow-sm border-0">
          <div class="card-body">
            <div class="text-primary display-4 mb-3"><i class="fas fa-calendar-alt"></i></div>
            <h3 class="h5 fw-bold mb-3">Class Booking</h3>
            <p class="text-muted">Reserve your spot in classes, track attendance, and manage your schedule all in one place.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4 fade-in delay-2">
        <div class="card h-100 text-center shadow-sm border-0">
          <div class="card-body">
            <div class="text-primary display-4 mb-3"><i class="fas fa-chart-line"></i></div>
            <h3 class="h5 fw-bold mb-3">Progress Tracking</h3>
            <p class="text-muted">Log workouts, track metrics, and visualize your improvement over time with detailed analytics.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="py-5 bg-white">
  <div class="container">
    <div class="row text-center g-4">
      <div class="col fade-in">
        <div class="display-4 fw-bold text-primary mb-2">10+</div>
        <div class="fs-5 text-muted">Years Experience</div>
      </div>
      <div class="col fade-in delay-1">
        <div class="display-4 fw-bold text-primary mb-2">5000+</div>
        <div class="fs-5 text-muted">Happy Members</div>
      </div>
      <div class="col fade-in delay-2">
        <div class="display-4 fw-bold text-primary mb-2">50+</div>
        <div class="fs-5 text-muted">Expert Trainers</div>
      </div>
    </div>
  </div>
</section>

<section id="about" class="py-5 bg-light">
  <div class="container">
    <div class="text-center mb-5 fade-in">
      <h2 class="fw-bold display-6 mb-3">WHY CHOOSE <span class="text-primary">SMART GYM</span></h2>
      <div class="mx-auto mb-4" style="width:80px;height:4px;background-color:#0d6efd;"></div>
      <p class="text-muted mx-auto" style="max-width:600px;">We're not just a gym, we're a community dedicated to helping you achieve your fitness goals with the best equipment, trainers, and programs.</p>
    </div>
    <div class="row g-4">
      <div class="col-md-4 fade-in">
        <div class="card h-100 text-center shadow-sm border-0">
          <div class="card-body">
            <div class="text-primary display-4 mb-3"><i class="fas fa-dumbbell"></i></div>
            <h3 class="h5 fw-bold mb-3">Premium Equipment</h3>
            <p class="text-muted">Our facility is equipped with the latest and highest quality fitness equipment from top brands.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4 fade-in delay-1">
        <div class="card h-100 text-center shadow-sm border-0">
          <div class="card-body">
            <div class="text-primary display-4 mb-3"><i class="fas fa-users"></i></div>
            <h3 class="h5 fw-bold mb-3">Expert Trainers</h3>
            <p class="text-muted">Our certified trainers have years of experience and will create personalized workout plans for you.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4 fade-in delay-2">
        <div class="card h-100 text-center shadow-sm border-0">
          <div class="card-body">
            <div class="text-primary display-4 mb-3"><i class="fas fa-heartbeat"></i></div>
            <h3 class="h5 fw-bold mb-3">Health First</h3>
            <p class="text-muted">We focus on safe, effective workouts that prioritize your long-term health and well-being.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="classes" class="py-5 bg-white">
  <div class="container">
    <div class="text-center mb-5 fade-in">
      <h2 class="fw-bold display-6 mb-3">OUR <span class="text-primary">CLASSES</span></h2>
      <div class="mx-auto mb-4" style="width:80px;height:4px;background-color:#0d6efd;"></div>
      <p class="text-muted mx-auto" style="max-width:600px;">We offer a wide variety of classes to suit all fitness levels and preferences.</p>
    </div>
    <div class="row g-4">
      <?php foreach ($classes as $index => $class): ?>
      <div class="col-md-6 col-lg-4 fade-in <?php echo $index == 1 ? 'delay-1' : ($index == 2 ? 'delay-2' : ''); ?>">
        <div class="card h-100 shadow-sm border-0">
          <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=1470&q=80" alt="Clase <?php echo htmlspecialchars($class['title']); ?>" class="card-img-top">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h3 class="h5 fw-bold mb-0"><?php echo htmlspecialchars($class['title']); ?></h3>
              <span class="badge bg-primary"><?php echo htmlspecialchars($class['activity_name']); ?></span>
            </div>
            <p class="text-muted mb-4"><?php echo htmlspecialchars($class['activity_description']); ?></p>
            <?php
              $start = new DateTime($class['start_at']);
              $end = new DateTime($class['end_at']);
              $durationMin = $start->diff($end)->i + ($start->diff($end)->h * 60);
            ?>
            <div class="d-flex justify-content-between text-muted">
              <span><i class="fas fa-clock me-1"></i> <?php echo $durationMin; ?> min</span>
              <span><i class="fas fa-user me-1"></i> Capacidad <?php echo htmlspecialchars($class['capacity']); ?></span>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-4 fade-in">
      <a href="/smartgym/pages/classes.php" class="btn btn-primary btn-lg">View All Classes</a>
    </div>
  </div>
</section>

<section id="trainers" class="py-5 bg-light">
  <div class="container">
    <div class="text-center mb-5 fade-in">
      <h2 class="fw-bold display-6 mb-3">OUR <span class="text-primary">TRAINERS</span></h2>
      <div class="mx-auto mb-4" style="width:80px;height:4px;background-color:#0d6efd;"></div>
      <p class="text-muted mx-auto" style="max-width:600px;">Meet our team of certified fitness professionals dedicated to helping you reach your goals.</p>
    </div>
    <div class="row g-4">
      <?php foreach ($trainers as $index => $trainer): ?>
      <div class="col-md-6 col-lg-3 fade-in <?php echo $index==1?'delay-1':($index==2?'delay-2':($index==3?'delay-3':'')); ?>">
        <div class="card h-100 trainer-card border-0 shadow-sm">
          <img src="<?php echo !empty($trainer['image_url']) ? htmlspecialchars($trainer['image_url']) : '/smartgym/assets/img/default-trainer.jpg'; ?>" alt="Trainer <?php echo htmlspecialchars($trainer['name']); ?>" class="card-img-top">
          <div class="card-body">
            <h3 class="h5 fw-bold mb-1"><?php echo htmlspecialchars($trainer['name']); ?></h3>
            <span class="text-primary small fw-semibold"><?php echo htmlspecialchars($trainer['specialty']); ?></span>
            <p class="text-muted mt-2 small"><?php echo htmlspecialchars($trainer['description']); ?></p>
            <div class="d-flex gap-3 mt-3">
              <?php if ($trainer['facebook']): ?><a href="<?php echo htmlspecialchars($trainer['facebook']); ?>" class="text-muted hover-primary"><i class="fab fa-facebook-f"></i></a><?php endif; ?>
              <?php if ($trainer['twitter']): ?><a href="<?php echo htmlspecialchars($trainer['twitter']); ?>" class="text-muted hover-primary"><i class="fab fa-twitter"></i></a><?php endif; ?>
              <?php if ($trainer['instagram']): ?><a href="<?php echo htmlspecialchars($trainer['instagram']); ?>" class="text-muted hover-primary"><i class="fab fa-instagram"></i></a><?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section id="membership" class="py-5 bg-white">
  <div class="container">
    <div class="text-center mb-5 fade-in">
      <h2 class="fw-bold display-6 mb-3">MEMBERSHIP <span class="text-primary">PLANS</span></h2>
      <div class="mx-auto mb-4" style="width:80px;height:4px;background-color:#0d6efd;"></div>
      <p class="text-muted mx-auto" style="max-width:600px;">Choose the plan that fits your fitness journey. All plans include access to our premium facilities.</p>
      <?php if ($loggedIn): ?>
        <a href="/smartgym/pages/membership_create.php" class="btn btn-success mt-3">Add Plan</a>
      <?php endif; ?>
    </div>
    <div class="row g-4 justify-content-center">
      <?php foreach ($memberships as $m): ?>
      <div class="col-md-4">
        <div class="card membership-card h-100 text-center border-0 shadow-sm">
          <div class="card-body">
            <h3 class="h5 fw-bold mb-3"><?php echo htmlspecialchars($m['name']); ?></h3>
            <p class="display-6"><strong>$<?php echo number_format($m['price'], 2); ?></strong><small class="text-muted">/<?php echo htmlspecialchars($m['duration_days']); ?> days</small></p>
            <form method="post" action="/smartgym/pages/cart.php" class="d-grid gap-2">
              <input type="hidden" name="membership_id" value="<?php echo $m['id']; ?>">
              <button class="btn btn-primary">Add to Cart</button>
            </form>
            <?php if ($loggedIn): ?>
            <div class="mt-3 d-flex justify-content-center gap-2">
              <a class="btn btn-sm btn-outline-primary" href="/smartgym/pages/membership_edit.php?id=<?php echo (int)$m['id']; ?>">Edit</a>
              <a class="btn btn-sm btn-outline-danger" href="/smartgym/pages/membership_delete.php?id=<?php echo (int)$m['id']; ?>">Delete</a>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
