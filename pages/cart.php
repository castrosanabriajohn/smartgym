<?php
// --- LÓGICA (sin salida) ---
require_once __DIR__ . '/../config.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$_SESSION['cart'] = $_SESSION['cart'] ?? [];

// Flash message (después de redirect)
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

// Add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['membership_id'])) {
    $id = (int)$_POST['membership_id'];
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
    header('Location: /smartgym/pages/cart.php'); exit;
}

// Remove
if (isset($_GET['remove'])) {
    $id = (int)$_GET['remove'];
    unset($_SESSION['cart'][$id]);
    header('Location: /smartgym/pages/cart.php'); exit;
}

// Build items desde sesión (GET normal)
$cartItems = [];
if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $ph  = implode(',', array_fill(0, count($ids), '?'));
    $st  = $db->prepare("SELECT id, name, price, duration_days FROM membership_plans WHERE id IN ($ph)");
    $st->execute($ids);
    foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $qty = (int)($_SESSION['cart'][$row['id']] ?? 0);
        if ($qty > 0) {
            $cartItems[] = [
                'id' => (int)$row['id'],
                'name' => $row['name'],
                'price' => (float)$row['price'],
                'duration_days' => (int)$row['duration_days'],
                'quantity' => $qty,
                'subtotal' => (float)$row['price'] * $qty,
            ];
        }
    }
}

// Checkout
if (isset($_POST['checkout'])) {
    if (empty($_SESSION['user_id'])) { header('Location: /smartgym/pages/login.php'); exit; }

    $userId = (int)$_SESSION['user_id'];
    $db->beginTransaction();
    try {
        foreach ($cartItems as $item) {
            for ($i = 0; $i < $item['quantity']; $i++) {
                $start = date('Y-m-d');
                $end   = date('Y-m-d', strtotime("+{$item['duration_days']} days"));

                $stmtUM = $db->prepare(
                    "INSERT INTO user_memberships (user_id, plan_id, start_date, end_date, status)
                     VALUES (:user_id, :plan_id, :start_date, :end_date, 'ACTIVE')"
                );
                $stmtUM->execute([
                    ':user_id' => $userId,
                    ':plan_id' => $item['id'],
                    ':start_date' => $start,
                    ':end_date' => $end
                ]);
                $membershipId = $db->lastInsertId();

                $stmtPay = $db->prepare(
                    "INSERT INTO payments (user_id, membership_id, reservation_id, method, amount, paid_at)
                     VALUES (:user_id, :membership_id, NULL, 'CARD', :amount, NOW())"
                );
                $stmtPay->execute([
                    ':user_id' => $userId,
                    ':membership_id' => $membershipId,
                    ':amount' => $item['price']
                ]);
            }
        }
        $db->commit();

        // Limpia y redirige (PRG)
        $_SESSION['cart'] = [];
        $_SESSION['flash'] = '¡Gracias por tu compra! Tu membresía ha sido activada.';
        header('Location: /smartgym/pages/cart.php'); exit;

    } catch (Exception $e) {
        $db->rollBack();
        $_SESSION['flash'] = 'Ocurrió un error procesando el pago. Intenta nuevamente.';
        header('Location: /smartgym/pages/cart.php'); exit;
    }
}

// --- VISTA ---
require_once __DIR__ . '/../includes/header.php';
?>
<div class="container mx-auto px-6 py-16">
    <h2 class="text-3xl font-bold mb-6 text-center">Shopping Cart</h2>

    <?php if (!empty($flash)): ?>
        <div class="bg-green-100 text-green-800 p-3 rounded mb-6 text-sm text-center max-w-md mx-auto">
            <?php echo htmlspecialchars($flash); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($cartItems)): ?>
        <p class="text-center text-gray-600">
            Your cart is empty. Browse our
            <a href="/smartgym/index.php#membership" class="text-blue-600 hover:underline">membership plans</a>.
        </p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow rounded-lg">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Plan</th>
                        <th class="py-3 px-4 text-right text-sm font-semibold text-gray-700">Price</th>
                        <th class="py-3 px-4 text-right text-sm font-semibold text-gray-700">Qty</th>
                        <th class="py-3 px-4 text-right text-sm font-semibold text-gray-700">Subtotal</th>
                        <th class="py-3 px-4"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total = 0; foreach ($cartItems as $item): $total += $item['subtotal']; ?>
                    <tr class="border-b">
                        <td class="py-3 px-4 text-sm font-medium text-gray-700">
                            <?php echo htmlspecialchars($item['name']); ?> (<?php echo (int)$item['duration_days']; ?> días)
                        </td>
                        <td class="py-3 px-4 text-right text-sm text-gray-700">$<?php echo number_format($item['price'], 2); ?></td>
                        <td class="py-3 px-4 text-right text-sm text-gray-700"><?php echo (int)$item['quantity']; ?></td>
                        <td class="py-3 px-4 text-right text-sm text-gray-700">$<?php echo number_format($item['subtotal'], 2); ?></td>
                        <td class="py-3 px-4 text-right">
                            <a href="/smartgym/pages/cart.php?remove=<?php echo (int)$item['id']; ?>" class="text-red-500 hover:underline text-sm">Remove</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" class="py-3 px-4 text-right font-semibold">Total</td>
                        <td class="py-3 px-4 text-right font-semibold">$<?php echo number_format($total, 2); ?></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <form method="post" class="mt-6 text-center">
            <button type="submit" name="checkout" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded">
                Checkout
            </button>
        </form>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
