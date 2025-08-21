<?php
// footer.php
// This file outputs the footer and closes the HTML document. It
// should be included at the end of every page.
?>
    </main>
    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-10 mt-10">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- About -->
                <div>
                    <h3 class="text-white text-lg font-bold mb-4">About SmartGym</h3>
                    <p class="text-sm">SmartGym is a community focused on improving health and wellness through modern facilities, personalized training and cutting-edge technology. Join us and be part of our fitness family.</p>
                </div>
                <!-- Quick Links -->
                <div>
                    <h3 class="text-white text-lg font-bold mb-4">Quick Links</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/smartgym/index.php#classes" class="hover:text-blue-400">Classes</a></li>
                        <li><a href="/smartgym/index.php#trainers" class="hover:text-blue-400">Trainers</a></li>
                        <li><a href="/smartgym/index.php#membership" class="hover:text-blue-400">Membership Plans</a></li>
                        <li><a href="/smartgym/pages/news.php" class="hover:text-blue-400">News</a></li>
                        <li><a href="/smartgym/pages/contact.php" class="hover:text-blue-400">Contact</a></li>
                    </ul>
                </div>
                <!-- Contact -->
                <div>
                    <h3 class="text-white text-lg font-bold mb-4">Get In Touch</h3>
                    <p class="text-sm">123 Fitness Ave.<br> San José, Costa Rica</p>
                    <p class="text-sm mt-2">Phone: +506 1234 5678</p>
                    <p class="text-sm">Email: info@smartgym.cr</p>
                    <div class="flex mt-4 space-x-4">
                        <a href="#" class="text-gray-400 hover:text-blue-400"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-gray-400 hover:text-blue-400"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-pink-600"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-gray-400 hover:text-red-600"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="text-center mt-8 text-gray-500 text-xs">
                © <?php echo date('Y'); ?> SmartGym. All rights reserved.
            </div>
        </div>
    </footer>
</body>
</html>