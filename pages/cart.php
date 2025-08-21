<?php
require_once __DIR__ . '/../includes/header.php';

// Initialize cart in session if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle add to cart POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['membership_id'])) {
    $membershipId = (int)$_POST['membership_id'];
    // Add membership to cart or increase quantity
    if (isset($_SESSION['cart'][$membershipId])) {
        $_SESSION['cart'][$membershipId]++;
    } else {
        $_SESSION['cart'][$membershipId] = 1;
    }
    header('Location: /smartgym/pages/cart.php');
    exit;
}

// Handle remove from cart
if (isset($_GET['remove'])) {
    $removeId = (int)$_GET['remove'];
    unset($_SESSION['cart'][$removeId]);
    header('Location: /smartgym/pages/cart.php');
    exit;
}

// Handle checkout
if (isset($_POST['checkout'])) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /smartgym/pages/login.php');
        exit;
    }
    $userId = $_SESSION['user_id'];
    foreach ($cartItems as $item) {
        for ($i = 0; $i < $item['quantity']; $i++) {
            $startDate = date('Y-m-d');
            $endDate   = date('Y-m-d', strtotime("+{$item['duration_days']} days"));
            // Insert user membership
            $stmtUM = $db->prepare('INSERT INTO user_memberships (user_id, plan_id, start_date, end_date, status) VALUES (:user_id,:plan_id,:start_date,:end_date, "ACTIVE")');
            $stmtUM->execute([
                ':user_id' => $userId,
                ':plan_id' => $item['id'],
                ':start_date' => $startDate,
                ':end_date'   => $endDate
            ]);
            $membershipId = $db->lastInsertId();
            // Insert payment record
            $stmtPay = $db->prepare('INSERT INTO payments (user_id, membership_id, reservation_id, method, amount, paid_at) VALUES (:user_id, :membership_id, NULL, "CARD", :amount, NOW())');
            $stmtPay->execute([
                ':user_id' => $userId,
                ':membership_id' => $membershipId,
                ':amount' => $item['price']
            ]);
        }
    }
    // Clear cart
    $_SESSION['cart'] = [];
    $checkoutMessage = '¡Gracias por tu compra! Tu membresía ha sido activada.';
}

// Fetch membership data for items in cart
$cartItems = [];
if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $stmt = $db->query("SELECT * FROM membership_plans WHERE id IN ($ids)");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($items as $item) {
        $id = $item['id'];
        $quantity = $_SESSION['cart'][$id];
        $cartItems[] = [
            'id' => $id,
            'name' => $item['name'],
            'price' => $item['price'],
            'duration_days' => $item['duration_days'],
            'quantity' => $quantity,
            'subtotal' => $item['price'] * $quantity
        ];
    }
}
?>
<div class="container mx-auto px-6 py-16">
    <h2 class="text-3xl font-bold mb-6 text-center">Shopping Cart</h2>
    <?php if (!empty($checkoutMessage)): ?>
        <div class="bg-green-100 text-green-800 p-3 rounded mb-6 text-sm text-center max-w-md mx-auto">
            <?php echo $checkoutMessage; ?>
        </div>
    <?php endif; ?>
    <?php if (empty($cartItems)): ?>
        <p class="text-center text-gray-600">Your cart is empty. Browse our <a href="/smartgym/index.php#membership" class="text-blue-600 hover:underline">membership plans</a> to get started.</p>
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
                    <?php $total = 0; ?>
                    <?php foreach ($cartItems as $item): ?>
                    <?php $total += $item['subtotal']; ?>
                    <tr class="border-b">
                        <td class="py-3 px-4 text-sm font-medium text-gray-700"><?php echo htmlspecialchars($item['name']); ?> (<?php echo htmlspecialchars($item['duration_days']); ?> días)</td>
                        <td class="py-3 px-4 text-right text-sm text-gray-700">
                            $<?php echo number_format($item['price'], 2); ?>
                        </td>
                        <td class="py-3 px-4 text-right text-sm text-gray-700">
                            <?php echo $item['quantity']; ?>
                        </td>
                        <td class="py-3 px-4 text-right text-sm text-gray-700">
                            $<?php echo number_format($item['subtotal'], 2); ?>
                        </td>
                        <td class="py-3 px-4 text-right">
                            <a href="/smartgym/pages/cart.php?remove=<?php echo $item['id']; ?>" class="text-red-500 hover:underline text-sm">Remove</a>
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
            <button type="submit" name="checkout" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded">Checkout</button>
        </form>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>