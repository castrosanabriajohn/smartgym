<?php
// pages/gallery.php — Galería + Video + Documentos
require_once __DIR__ . '/../config.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$canManage = isset($_SESSION['user_id']);

$galleryDir = __DIR__ . '/../uploads/gallery/';
$docsDir    = __DIR__ . '/../uploads/docs/';
@is_dir($galleryDir) || @mkdir($galleryDir, 0775, true);
@is_dir($docsDir)    || @mkdir($docsDir, 0775, true);

$message = '';
$error   = '';

// ---------- Acciones ----------
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

require_once __DIR__ . '/../includes/header.php';
?>
<div class="container py-5">
  <h2 class="text-center mb-4">Gallery</h2>

  <?php if ($message): ?>
    <div class="alert alert-success text-center mx-auto mb-4" style="max-width:600px;"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="alert alert-danger text-center mx-auto mb-4" style="max-width:600px;"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if ($canManage): ?>
  <form method="post" enctype="multipart/form-data" class="mx-auto mb-4 bg-white shadow p-3 rounded d-flex align-items-center gap-3" style="max-width:600px;">
    <input type="hidden" name="action" value="upload_image">
    <input type="file" name="image" accept="image/*" class="form-control" required>
    <button class="btn btn-primary">Upload Image</button>
  </form>
  <?php endif; ?>

  <div id="grid" class="d-flex gap-3 overflow-auto mb-5">
    <?php foreach ($images as $img): ?>
      <div class="position-relative" style="flex:0 0 auto;width:150px;" data-id="<?= (int)$img['id']; ?>" draggable="<?= $canManage ? 'true':'false'; ?>">
        <img src="/smartgym/uploads/gallery/<?= htmlspecialchars($img['filename']); ?>" alt="img" class="img-fluid rounded" style="height:120px;object-fit:cover;">
        <?php if ($canManage): ?>
          <span class="position-absolute top-0 start-0 text-white bg-dark bg-opacity-50 small px-1">↕</span>
          <form method="post" class="position-absolute top-0 end-0 m-1" onsubmit="return confirm('¿Borrar imagen?');">
            <input type="hidden" name="action" value="delete_image">
            <input type="hidden" name="id" value="<?= (int)$img['id']; ?>">
            <button class="btn btn-danger btn-sm">&times;</button>
          </form>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="mt-5">
    <h3 class="h5 mb-3">Featured Video</h3>
    <div class="ratio ratio-16x9">
      <iframe src="https://www.youtube.com/embed/wnHW6o8WMas" title="Fitness Video" allowfullscreen></iframe>
    </div>
  </div>

  <div class="mt-5">
    <h3 class="h5 mb-3">Upload Documents</h3>
    <form method="post" enctype="multipart/form-data" class="bg-white shadow p-3 rounded d-flex align-items-center gap-3" style="max-width:600px;">
      <input type="hidden" name="action" value="upload_doc">
      <input type="file" name="document" class="form-control" required>
      <button class="btn btn-primary">Upload</button>
      <span class="small text-muted">PDF/DOC/DOCX/JPG/PNG • máx 5MB</span>
    </form>
  </div>

  <div class="mt-4">
    <h3 class="h5 mb-3">Uploaded Documents</h3>
    <?php if (!$docs): ?>
      <p class="text-muted">No documents uploaded yet.</p>
    <?php else: ?>
      <ul class="list-group" style="max-width:600px;">
        <?php foreach ($docs as $d): ?>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span class="small text-break"><?= htmlspecialchars($d['filename']); ?></span>
            <div class="d-flex align-items-center gap-2">
              <a href="/smartgym/uploads/docs/<?= urlencode($d['filename']); ?>" class="text-primary small" download>Download</a>
              <form method="post" onsubmit="return confirm('¿Borrar documento?');">
                <input type="hidden" name="action" value="delete_doc">
                <input type="hidden" name="id" value="<?= (int)$d['id']; ?>">
                <button class="btn btn-link text-danger p-0 small">Delete</button>
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
  const after = (e.clientX - rect.left) / rect.width > 0.5;
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
