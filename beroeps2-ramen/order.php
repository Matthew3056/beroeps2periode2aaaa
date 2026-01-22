<?php
$page_title = "Bestelling - Ramen Delivery";
require_once 'includes/db.php';
require_once 'includes/header.php';

// Check of gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Bestelling plaatsen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $cart = json_decode($_POST['cart'], true);
    
    if (empty($cart) || !is_array($cart)) {
        $error = 'Je winkelwagen is leeg.';
    } else {
        try {
            $pdo->beginTransaction();
            
            // Bereken totaal
            $total = 0;
            foreach ($cart as $item) {
                $stmt = $pdo->prepare("SELECT price FROM dishes WHERE id = ?");
                $stmt->execute([$item['dish_id']]);
                $dish = $stmt->fetch();
                if ($dish) {
                    $total += $dish['price'] * $item['quantity'];
                }
            }
            
            // Maak bestelling
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total, status) VALUES (?, ?, 'pending')");
            $stmt->execute([$user_id, $total]);
            $order_id = $pdo->lastInsertId();
            
            // Voeg order items toe
            foreach ($cart as $item) {
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, dish_id, quantity) VALUES (?, ?, ?)");
                $stmt->execute([$order_id, $item['dish_id'], $item['quantity']]);
            }
            
            $pdo->commit();
            $success = 'Bestelling succesvol geplaatst!';
            
            // Clear cart in session
            unset($_SESSION['cart']);
        } catch(PDOException $e) {
            $pdo->rollBack();
            $error = 'Er is een fout opgetreden bij het plaatsen van de bestelling.';
        }
    }
}

// Haal bestellingen op voor deze gebruiker
try {
    // Gebruik een subquery om item_count te berekenen om duplicaten te voorkomen
    $stmt = $pdo->prepare("
        SELECT o.*, 
               (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
        FROM orders o
        WHERE o.user_id = ?
        ORDER BY o.created_at DESC
        LIMIT 10
    ");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll();
} catch(PDOException $e) {
    $orders = [];
}
?>

<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-shopping-cart"></i> Mijn Bestellingen</h1>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>
    
    <div class="order-section">
        <div class="cart-section">
            <h2><i class="fas fa-shopping-basket"></i> Winkelwagen</h2>
            <div id="cartItems" class="cart-items">
                <!-- Cart items worden hier dynamisch ingeladen via JavaScript -->
            </div>
            <div class="cart-total" id="cartTotal">
                <strong>Totaal: €0,00</strong>
            </div>
            <form method="POST" id="placeOrderForm" style="display: none;">
                <input type="hidden" name="cart" id="cartData">
                <button type="submit" name="place_order" class="btn btn-primary btn-block">
                    <i class="fas fa-check"></i> Bestelling Plaatsen
                </button>
            </form>
        </div>
        
        <div class="orders-history">
            <h2><i class="fas fa-history"></i> Bestelgeschiedenis</h2>
            <?php if (empty($orders)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>Je hebt nog geen bestellingen geplaatst.</p>
                </div>
            <?php else: ?>
                <div class="orders-list">
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <span class="order-id">Bestelling #<?php echo $order['id']; ?></span>
                                <span class="order-status status-<?php echo $order['status']; ?>">
                                    <?php
                                    $status_labels = [
                                        'pending' => 'In behandeling',
                                        'preparing' => 'Wordt bereid',
                                        'delivering' => 'Onderweg',
                                        'completed' => 'Voltooid'
                                    ];
                                    echo $status_labels[$order['status']] ?? $order['status'];
                                    ?>
                                </span>
                            </div>
                            <div class="order-details">
                                <p><i class="fas fa-euro-sign"></i> Totaal: €<?php echo number_format($order['total'], 2, ',', '.'); ?></p>
                                <p><i class="fas fa-box"></i> Items: <?php echo $order['item_count']; ?></p>
                                <p><i class="fas fa-calendar"></i> <?php echo date('d-m-Y H:i', strtotime($order['created_at'])); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
