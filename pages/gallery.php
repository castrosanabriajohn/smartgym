<?php
require_once __DIR__ . '/../includes/header.php';

// Fetch images for gallery from classes and trainers tables
$classStmt = $db->query('SELECT image_url FROM classes');
$classImages = $classStmt->fetchAll(PDO::FETCH_COLUMN);
$trainerStmt = $db->query('SELECT image_url FROM trainers');
$trainerImages = $trainerStmt->fetchAll(PDO::FETCH_COLUMN);
$galleryImages = array_merge($classImages, $trainerImages);

$uploadMessage = '';

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document'])) {
    $file = $_FILES['document'];
    if ($file['error'] === UPLOAD_ERR_OK) {
        $allowed = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx'=> 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'jpg' => 'image/jpeg',
            'jpeg'=> 'image/jpeg',
            'png' => 'image/png'
        ];
        $fileName = $file['name'];
        $fileTmp = $file['tmp_name'];
        $fileSize = $file['size'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $fileTmp);
        finfo_close($finfo);
        if (!isset($allowed[$ext]) || $allowed[$ext] !== $mimeType) {
            $uploadMessage = 'Invalid file type.';
        } elseif ($fileSize > 5 * 1024 * 1024) { // 5MB limit
            $uploadMessage = 'File too large (max 5MB).';
        } else {
            $uniqueName = uniqid('upload_') . '.' . $ext;
            $destination = __DIR__ . '/../uploads/' . $uniqueName;
            if (move_uploaded_file($fileTmp, $destination)) {
                // Record in database
                $userId = $_SESSION['user_id'] ?? null;
                $stmt = $db->prepare('INSERT INTO uploads (user_id, filename) VALUES (:user_id, :filename)');
                $stmt->execute([
                    ':user_id' => $userId,
                    ':filename' => $uniqueName
                ]);
                $uploadMessage = 'File uploaded successfully.';
            } else {
                $uploadMessage = 'Failed to upload file.';
            }
        }
    } else {
        $uploadMessage = 'No file selected or upload error.';
    }
}

// Retrieve list of uploaded documents
$stmt = $db->query('SELECT id, filename, uploaded_at FROM uploads ORDER BY uploaded_at DESC');
$uploadedDocs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container mx-auto px-6 py-16">
    <h2 class="text-3xl font-bold mb-6 text-center">Gallery &amp; Documents</h2>
    <!-- Image Gallery with Drag and Drop -->
    <div class="mb-12">
        <h3 class="text-xl font-semibold mb-4">Image Gallery (Drag images into Favorites)</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="gallery">
            <?php foreach ($galleryImages as $img): ?>
            <div class="relative">
                <img src="<?php echo htmlspecialchars($img); ?>" alt="Gallery Image" class="w-full h-40 object-cover rounded draggable" draggable="true">
            </div>
            <?php endforeach; ?>
        </div>
        <div class="mt-8">
            <h4 class="text-lg font-semibold mb-2">Favorites</h4>
            <div id="favorites" class="min-h-[100px] p-4 border-2 border-dashed border-gray-300 rounded"></div>
            <button id="clear-favorites" class="mt-4 px-4 py-2 bg-red-500 text-white rounded hidden">Clear Favorites</button>
        </div>
    </div>
    <!-- Video Section -->
    <div class="mb-12">
        <h3 class="text-xl font-semibold mb-4">Featured Video</h3>
        <div class="aspect-w-16 aspect-h-9">
            <iframe width="560" height="315" src="https://www.youtube.com/embed/dO0GBOodJ80" title="Fitness Video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen class="w-full h-64 md:h-96 rounded"></iframe>
        </div>
    </div>
    <!-- Document Upload -->
    <div class="mb-12">
        <h3 class="text-xl font-semibold mb-4">Upload Documents</h3>
        <?php if ($uploadMessage): ?>
            <div class="bg-blue-100 text-blue-800 p-3 rounded mb-4 text-sm">
                <?php echo htmlspecialchars($uploadMessage); ?>
            </div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <div class="flex items-center space-x-4">
                <input type="file" name="document" class="border p-2 rounded">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Upload</button>
            </div>
        </form>
    </div>
    <!-- Uploaded Documents List -->
    <div>
        <h3 class="text-xl font-semibold mb-4">Uploaded Documents</h3>
        <?php if (empty($uploadedDocs)): ?>
            <p class="text-gray-600">No documents uploaded yet.</p>
        <?php else: ?>
            <ul class="space-y-2">
                <?php foreach ($uploadedDocs as $doc): ?>
                <li class="flex items-center justify-between bg-gray-100 p-3 rounded">
                    <span class="text-sm break-all"><?php echo htmlspecialchars($doc['filename']); ?></span>
                    <a href="/smartgym/uploads/<?php echo urlencode($doc['filename']); ?>" class="text-blue-600 hover:underline text-sm" download>Download</a>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
<script>
// Drag and drop functionality
const gallery = document.getElementById('gallery');
const favorites = document.getElementById('favorites');
const clearBtn = document.getElementById('clear-favorites');

// Add dragstart event to gallery images
gallery.addEventListener('dragstart', (e) => {
    if (e.target.tagName === 'IMG') {
        e.dataTransfer.setData('text/plain', e.target.src);
    }
});

// Allow dropping on favorites
favorites.addEventListener('dragover', (e) => {
    e.preventDefault();
});

favorites.addEventListener('drop', (e) => {
    e.preventDefault();
    const imageUrl = e.dataTransfer.getData('text/plain');
    const img = document.createElement('img');
    img.src = imageUrl;
    img.className = 'w-20 h-20 object-cover rounded mr-2 mb-2 inline-block';
    favorites.appendChild(img);
    clearBtn.classList.remove('hidden');
});

clearBtn.addEventListener('click', () => {
    favorites.innerHTML = '';
    clearBtn.classList.add('hidden');
});
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>