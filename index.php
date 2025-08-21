<?php
// index.php
// Main landing page of the SmartGym web application. It composes
// various sections such as hero, features, stats, about, classes,
// trainers and memberships, pulling dynamic content from the database
// where appropriate.
require_once __DIR__ . '/includes/header.php';

$stmt = $db->prepare('SELECT c.id, c.title, c.start_at, c.end_at, c.capacity, c.trainer, a.name AS activity_name, a.description AS activity_description
                      FROM classes c
                      JOIN activity_types a ON c.activity_type_id = a.id
                      ORDER BY c.start_at ASC
                      LIMIT 3');
$stmt->execute();
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->query('SELECT * FROM trainers');
$trainers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Retrieve membership plans
$stmt = $db->query('SELECT * FROM membership_plans');
$memberships = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- Hero Section -->
<section id="home" class="hero-gradient text-white py-20 md:py-32">
    <div class="container mx-auto px-6 flex flex-col md:flex-row items-center">
        <div class="md:w-1/2 mb-12 md:mb-0 fade-in">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">BUILD YOUR <span class="text-blue-400">PERFECT</span> BODY</h1>
            <p class="text-xl mb-8 text-gray-300">Join our premium fitness community and transform your body with our state-of-the-art facilities and expert trainers.</p>
            <div class="flex space-x-4">
                <a href="/smartgym/pages/register.php" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition duration-300 transform hover:scale-105">Join Now</a>
                <a href="#classes" class="bg-transparent border-2 border-white hover:bg-white hover:text-gray-900 text-white px-8 py-3 rounded-lg font-semibold transition duration-300">Our Classes</a>
            </div>
        </div>
        <div class="md:w-1/2 fade-in delay-1">
            <!-- Background hero image -->
            <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=1470&q=80" alt="Gym Hero Image" class="rounded-lg shadow-2xl">
        </div>
    </div>
</section>

<!-- Platform Features Section -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16 fade-in">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">YOUR <span class="text-blue-600">DIGITAL FITNESS PLATFORM</span></h2>
            <div class="w-20 h-1 bg-blue-600 mx-auto mb-6"></div>
            <p class="text-gray-600 max-w-2xl mx-auto">Access all your fitness tools in one place with our integrated platform</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="bg-gray-100 p-8 rounded-lg shadow-lg hover:shadow-xl transition duration-300 fade-in">
                <div class="text-blue-600 text-4xl mb-4">
                    <i class="fas fa-user-cog"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Personalized Profiles</h3>
                <p class="text-gray-600">Track your progress, set goals, and get customized workout plans based on your objectives.</p>
            </div>
            <!-- Feature 2 -->
            <div class="bg-gray-100 p-8 rounded-lg shadow-lg hover:shadow-xl transition duration-300 fade-in delay-1">
                <div class="text-blue-600 text-4xl mb-4">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Class Booking</h3>
                <p class="text-gray-600">Reserve your spot in classes, track attendance, and manage your schedule all in one place.</p>
            </div>
            <!-- Feature 3 -->
            <div class="bg-gray-100 p-8 rounded-lg shadow-lg hover:shadow-xl transition duration-300 fade-in delay-2">
                <div class="text-blue-600 text-4xl mb-4">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Progress Tracking</h3>
                <p class="text-gray-600">Log workouts, track metrics, and visualize your improvement over time with detailed analytics.</p>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="bg-white py-16">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
            <div class="fade-in">
                <div class="text-5xl font-bold text-blue-600 mb-2">10+</div>
                <div class="text-xl text-gray-600">Years Experience</div>
            </div>
            <div class="fade-in delay-1">
                <div class="text-5xl font-bold text-blue-600 mb-2">5000+</div>
                <div class="text-xl text-gray-600">Happy Members</div>
            </div>
            <div class="fade-in delay-2">
                <div class="text-5xl font-bold text-blue-600 mb-2">50+</div>
                <div class="text-xl text-gray-600">Expert Trainers</div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="py-20 bg-gray-100">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16 fade-in">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">WHY CHOOSE <span class="text-blue-600">SMART GYM</span></h2>
            <div class="w-20 h-1 bg-blue-600 mx-auto mb-6"></div>
            <p class="text-gray-600 max-w-2xl mx-auto">We're not just a gym, we're a community dedicated to helping you achieve your fitness goals with the best equipment, trainers, and programs.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-lg shadow-lg hover:shadow-xl transition duration-300 fade-in">
                <div class="text-blue-600 text-4xl mb-4">
                    <i class="fas fa-dumbbell"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Premium Equipment</h3>
                <p class="text-gray-600">Our facility is equipped with the latest and highest quality fitness equipment from top brands.</p>
            </div>
            <div class="bg-white p-8 rounded-lg shadow-lg hover:shadow-xl transition duration-300 fade-in delay-1">
                <div class="text-blue-600 text-4xl mb-4">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Expert Trainers</h3>
                <p class="text-gray-600">Our certified trainers have years of experience and will create personalized workout plans for you.</p>
            </div>
            <div class="bg-white p-8 rounded-lg shadow-lg hover:shadow-xl transition duration-300 fade-in delay-2">
                <div class="text-blue-600 text-4xl mb-4">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Health First</h3>
                <p class="text-gray-600">We focus on safe, effective workouts that prioritize your long-term health and well-being.</p>
            </div>
        </div>
    </div>
</section>

<!-- Classes Section -->
<section id="classes" class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16 fade-in">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">OUR <span class="text-blue-600">CLASSES</span></h2>
            <div class="w-20 h-1 bg-blue-600 mx-auto mb-6"></div>
            <p class="text-gray-600 max-w-2xl mx-auto">We offer a wide variety of classes to suit all fitness levels and preferences.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($classes as $index => $class): ?>
            <div class="bg-gray-100 rounded-lg overflow-hidden shadow-lg fade-in <?php echo $index == 1 ? 'delay-1' : ($index == 2 ? 'delay-2' : ''); ?>">
                <!-- Use a placeholder image for classes since image_url is not stored in this schema -->
                <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=1470&q=80" alt="Clase <?php echo htmlspecialchars($class['title']); ?>" class="w-full h-48 object-cover">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold"><?php echo htmlspecialchars($class['title']); ?></h3>
                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded"><?php echo htmlspecialchars($class['activity_name']); ?></span>
                    </div>
                    <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($class['activity_description']); ?></p>
                    <?php
                        $start = new DateTime($class['start_at']);
                        $end   = new DateTime($class['end_at']);
                        $durationMin = $start->diff($end)->i + ($start->diff($end)->h * 60);
                    ?>
                    <div class="flex justify-between text-sm text-gray-500">
                        <span><i class="fas fa-clock mr-1"></i> <?php echo $durationMin; ?> min</span>
                        <span><i class="fas fa-user mr-1"></i> Capacidad <?php echo htmlspecialchars($class['capacity']); ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-12 fade-in">
            <a href="/smartgym/pages/classes.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition duration-300">View All Classes</a>
        </div>
    </div>
</section>

<!-- Trainers Section -->
<section id="trainers" class="py-20 bg-gray-100">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16 fade-in">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">OUR <span class="text-blue-600">TRAINERS</span></h2>
            <div class="w-20 h-1 bg-blue-600 mx-auto mb-6"></div>
            <p class="text-gray-600 max-w-2xl mx-auto">Meet our team of certified fitness professionals dedicated to helping you reach your goals.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach ($trainers as $index => $trainer): ?>
            <div class="bg-white rounded-lg overflow-hidden shadow-lg trainer-card transition duration-300 fade-in <?php echo $index == 1 ? 'delay-1' : ($index == 2 ? 'delay-2' : ($index == 3 ? 'delay-3' : '')); ?>">
                <img src="<?php echo htmlspecialchars($trainer['image_url']); ?>" alt="Trainer <?php echo htmlspecialchars($trainer['name']); ?>" class="w-full h-64 object-cover">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-1"><?php echo htmlspecialchars($trainer['name']); ?></h3>
                    <span class="text-blue-600 text-sm font-semibold"><?php echo htmlspecialchars($trainer['specialty']); ?></span>
                    <p class="text-gray-600 mt-3 text-sm"><?php echo htmlspecialchars($trainer['description']); ?></p>
                    <div class="flex mt-4 space-x-3">
                        <?php if ($trainer['facebook']): ?><a href="<?php echo htmlspecialchars($trainer['facebook']); ?>" class="text-gray-500 hover:text-blue-600"><i class="fab fa-facebook-f"></i></a><?php endif; ?>
                        <?php if ($trainer['twitter']): ?><a href="<?php echo htmlspecialchars($trainer['twitter']); ?>" class="text-gray-500 hover:text-blue-400"><i class="fab fa-twitter"></i></a><?php endif; ?>
                        <?php if ($trainer['instagram']): ?><a href="<?php echo htmlspecialchars($trainer['instagram']); ?>" class="text-gray-500 hover:text-pink-600"><i class="fab fa-instagram"></i></a><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Membership Section -->
<section id="membership" class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16 fade-in">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">MEMBERSHIP <span class="text-blue-600">PLANS</span></h2>
            <div class="w-20 h-1 bg-blue-600 mx-auto mb-6"></div>
            <p class="text-gray-600 max-w-2xl mx-auto">Choose the plan that fits your fitness journey. All plans include access to our premium facilities.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <?php foreach ($memberships as $index => $m): ?>
            <div class="bg-gray-100 rounded-lg p-8 membership-card transition duration-300 fade-in <?php echo $index == 1 ? 'delay-1' : ($index == 2 ? 'delay-2' : ''); ?>">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($m['name']); ?></h3>
                    <div class="text-4xl font-bold text-blue-600 mb-2">
                        $<?php echo number_format($m['price'], 2); ?><span class="text-lg text-gray-500">/<?php echo htmlspecialchars($m['duration_days']); ?> días</span>
                    </div>
                </div>
                <ul class="space-y-3 mb-8">
                    <?php
                    // Define feature sets based on plan name
                    $planFeatures = [];
                    switch (strtolower($m['name'])) {
                        case 'basic':
                            $planFeatures = ['Acceso al gimnasio','Clases básicas'];
                            break;
                        case 'pro':
                            $planFeatures = ['Acceso al gimnasio','Todas las clases','Sesiones con entrenador personal'];
                            break;
                        case 'elite':
                            $planFeatures = ['Acceso al gimnasio','Todas las clases','Entrenador personal ilimitado','Plan de nutrición'];
                            break;
                        default:
                            $planFeatures = ['Acceso al gimnasio'];
                    }
                    $allFeatures = ['Acceso al gimnasio','Clases básicas','Todas las clases','Sesiones con entrenador personal','Entrenador personal ilimitado','Plan de nutrición'];
                    foreach ($allFeatures as $feature):
                        $included = in_array($feature, $planFeatures);
                    ?>
                    <li class="flex items-center <?php echo $included ? 'text-gray-600' : 'text-gray-400'; ?>">
                        <?php if ($included): ?>
                            <i class="fas fa-check text-green-500 mr-2"></i> <?php echo $feature; ?>
                        <?php else: ?>
                            <i class="fas fa-times text-red-400 mr-2"></i> <?php echo $feature; ?>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <form method="post" action="/smartgym/pages/cart.php">
                    <input type="hidden" name="membership_id" value="<?php echo $m['id']; ?>">
                    <button class="w-full <?php echo $index == 0 ? 'bg-gray-300 hover:bg-gray-400 text-gray-800' : ($index == 1 ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-purple-600 hover:bg-purple-700 text-white'); ?> font-semibold py-2 px-4 rounded-lg transition duration-300 flex items-center justify-center" type="submit">
                        Añadir al carrito
                    </button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>