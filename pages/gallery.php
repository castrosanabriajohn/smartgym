<?php
// pages/gallery.php — Galería + Video + Documentos
require_once __DIR__ . '/../config.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$canManage = isset($_SESSION['user_id']); // cámbialo si quieres solo admins

// Rutas
$galleryDir = __DIR__ . '/../uploads/gallery/';
$docsDir    = __DIR__ . '/../uploads/docs/';
@is_dir($galleryDir) || @mkdir($galleryDir, 0775, true);
@is_dir($docsDir)    || @mkdir($docsDir, 0775, true);

$message = '';
$error   = '';

// ---------- Acciones (antes del HTML) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // 1) Subir imagen a galería
    if ($action === 'upload_image' && isset($_FILES['image']) && $canManage) {
        $f = $_FILES['image'];
        if ($f['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
            $allowedExt  = ['jpg','jpeg','png','webp','gif'];
            $allowedMime = ['image/jpeg','image/png','image/webp','image/gif'];
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);

            if (!in_array($ext,$allowedExt,true) || !in_array($mime,$allowedMime,true)) {
                $error = 'Tipo de imagen inválido.';
            } elseif ($f['size'] > 5*1024*1024) {
                $error = 'Imagen muy grande (máx 5MB).';
            } else {
                $name = uniqid('img_', true) . '.' . $ext;
                if (move_uploaded_file($f['tmp_name'], $galleryDir.$name)) {
                    $next = (int)$db->query('SELECT COALESCE(MAX(sort_order),0)+1 FROM gallery_images')->fetchColumn();
                    $stmt = $db->prepare('INSERT INTO gallery_images (user_id, filename, sort_order) VALUES (:u,:f,:o)');
                    $stmt->execute([':u'=>$_SESSION['user_id'] ?? null, ':f'=>$name, ':o'=>$next]);
                    $message = 'Imagen subida.';
                } else $error = 'No se pudo guardar la imagen.';
            }
        } else $error = 'Error al subir imagen.';
    }

    // 2) Borrar imagen
    if ($action === 'delete_image' && $canManage) {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare('SELECT filename FROM gallery_images WHERE id=:id');
        $stmt->execute([':id'=>$id]);
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            @unlink($galleryDir.$row['filename']);
            $db->prepare('DELETE FROM gallery_images WHERE id=:id')->execute([':id'=>$id]);
            $message = 'Imagen borrada.';
        }
    }

    // 3) Reordenar (drag & drop) — AJAX
    if ($action === 'reorder' && $canManage) {
        $ids = $_POST['order'] ?? [];
        if (is_string($ids)) $ids = json_decode($ids,true) ?: [];
        header('Content-Type: application/json');
        if (!is_array($ids) || empty($ids)) { echo json_encode(['ok'=>false]); exit; }
        $db->beginTransaction();
        try {
            $pos = 1;
            $up = $db->prepare('UPDATE gallery_images SET sort_order=:p WHERE id=:id');
            foreach ($ids as $id) $up->execute([':p'=>$pos++, ':id'=>(int)$id]);
            $db->commit();
            echo json_encode(['ok'=>true]); exit;
        } catch (Throwable $e) { $db->rollBack(); echo json_encode(['ok'=>false]); exit; }
    }

    // 4) Subir documento
    if ($action === 'upload_doc' && isset($_FILES['document'])) {
        $f = $_FILES['document'];
        if ($f['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
            $allowed = [
                'pdf'=>'application/pdf',
                'doc'=>'application/msword',
                'docx'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png'
            ];
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
            if (!isset($allowed[$ext]) || $allowed[$ext] !== $mime) {
                $error = 'Tipo de archivo inválido.';
            } elseif ($f['size'] > 5*1024*1024) {
                $error = 'Archivo muy grande (máx 5MB).';
            } else {
                $name = uniqid('doc_', true).'.'.$ext;
                if (move_uploaded_file($f['tmp_name'], $docsDir.$name)) {
                    $stmt = $db->prepare('INSERT INTO uploads (user_id, filename) VALUES (:u,:f)');
                    $stmt->execute([':u'=>$_SESSION['user_id'] ?? null, ':f'=>$name]);
                    $message = 'Documento subido.';
                } else $error = 'No se pudo guardar el documento.';
            }
        } else $error = 'Error al subir documento.';
    }

    // 5) Borrar documento
    if ($action === 'delete_doc') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare('SELECT filename FROM uploads WHERE id=:id');
        $stmt->execute([':id'=>$id]);
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            @unlink($docsDir.$row['filename']);
            $db->prepare('DELETE FROM uploads WHERE id=:id')->execute([':id'=>$id]);
            $message = 'Documento borrado.';
        }
    }
}

// Cargar data para vista
$images = $db->query('SELECT id, filename FROM gallery_images ORDER BY sort_order ASC, id ASC')->fetchAll(PDO::FETCH_ASSOC);
$docs   = $db->query('SELECT id, filename, uploaded_at FROM uploads ORDER BY uploaded_at DESC')->fetchAll(PDO::FETCH_ASSOC);

// ---------- Vista ----------
require_once __DIR__ . '/../includes/header.php';
?>
<div class="container mx-auto px-6 py-16">
  <h2 class="text-3xl font-bold mb-6 text-center">Gallery</h2>

  <?php if ($message): ?>
    <div class="bg-green-100 text-green-800 p-3 rounded mb-6 text-sm max-w-xl mx-auto"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="bg-red-100 text-red-800 p-3 rounded mb-6 text-sm max-w-xl mx-auto"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if ($canManage): ?>
  <!-- Subir imagen -->
  <form method="post" enctype="multipart/form-data" class="max-w-xl mx-auto mb-10 bg-white shadow rounded p-4 flex items-center gap-4">
    <input type="hidden" name="action" value="upload_image">
    <input type="file" name="image" accept="image/*" class="border p-2 rounded w-full" required>
    <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">Subir imagen</button>
  </form>
  <?php endif; ?>

  <!-- Grid con drag & drop -->
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="grid">
    <?php foreach ($images as $img): ?>
      <div class="relative group" data-id="<?= (int)$img['id']; ?>" draggable="<?= $canManage ? 'true':'false'; ?>">
        <img src="/smartgym/uploads/gallery/<?= htmlspecialchars($img['filename']); ?>" alt="img" class="w-full h-40 object-cover rounded">
        <?php if ($canManage): ?>
          <div class="absolute inset-0 rounded bg-black/0 group-hover:bg-black/30 transition"></div>
          <div class="absolute top-2 left-2 text-white text-xs bg-black/50 px-2 py-1 rounded select-none">↕ Drag</div>
          <form method="post" class="absolute top-2 right-2" onsubmit="return confirm('¿Borrar imagen?');">
            <input type="hidden" name="action" value="delete_image">
            <input type="hidden" name="id" value="<?= (int)$img['id']; ?>">
            <button class="bg-red-600 hover:bg-red-700 text-white text-xs px-2 py-1 rounded">Borrar</button>
          </form>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Featured Video -->
  <div class="mt-12">
    <h3 class="text-xl font-semibold mb-4">Featured Video</h3>
    <div class="aspect-w-16 aspect-h-9">
      <iframe width="560" height="315"
              src="https://www.youtube.com/embed/wnHW6o8WMas"
              title="Fitness Video" frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              allowfullscreen class="w-full h-64 md:h-96 rounded"></iframe>
    </div>
  </div>

  <!-- Upload Documents -->
  <div class="mt-12">
    <h3 class="text-xl font-semibold mb-4">Upload Documents</h3>
    <form method="post" enctype="multipart/form-data" class="max-w-xl bg-white shadow rounded p-4 flex items-center gap-4">
      <input type="hidden" name="action" value="upload_doc">
      <input type="file" name="document" class="border p-2 rounded w-full" required>
      <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">Upload</button>
      <span class="text-xs text-gray-500">PDF/DOC/DOCX/JPG/PNG • máx 5MB</span>
    </form>
  </div>

  <!-- Lista de documentos -->
  <div class="mt-6">
    <h3 class="text-xl font-semibold mb-4">Uploaded Documents</h3>
    <?php if (!$docs): ?>
      <p class="text-gray-600">No documents uploaded yet.</p>
    <?php else: ?>
      <ul class="space-y-2 max-w-2xl">
        <?php foreach ($docs as $d): ?>
          <li class="flex items-center justify-between bg-gray-100 p-3 rounded">
            <span class="text-sm break-all"><?= htmlspecialchars($d['filename']); ?></span>
            <div class="flex items-center gap-3">
              <a href="/smartgym/uploads/docs/<?= urlencode($d['filename']); ?>" class="text-blue-600 hover:underline text-sm" download>Download</a>
              <form method="post" onsubmit="return confirm('¿Borrar documento?');">
                <input type="hidden" name="action" value="delete_doc">
                <input type="hidden" name="id" value="<?= (int)$d['id']; ?>">
                <button class="text-red-600 hover:underline text-sm">Delete</button>
              </form>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</div>

<?php if ($canManage): ?>
<script>
// Drag & drop para reordenar imágenes
const grid = document.getElementById('grid');
let dragging = null;

grid.addEventListener('dragstart', e => {
  const item = e.target.closest('[data-id]');
  if (!item) return;
  dragging = item;
  e.dataTransfer.effectAllowed = 'move';
});
grid.addEventListener('dragover', e => {
  e.preventDefault();
  const over = e.target.closest('[data-id]');
  if (!over || over === dragging) return;
  const rect = over.getBoundingClientRect();
  const after = (e.clientY - rect.top) / rect.height > 0.5;
  grid.insertBefore(dragging, after ? over.nextSibling : over);
});
grid.addEventListener('drop', e => { e.preventDefault(); saveOrder(); });
grid.addEventListener('dragend', () => saveOrder());

function saveOrder(){
  const ids = [...grid.querySelectorAll('[data-id]')].map(el => el.dataset.id);
  fetch('gallery.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'action=reorder&' + ids.map(id=>'order[]='+encodeURIComponent(id)).join('&')
  }).catch(()=>{});
}
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>