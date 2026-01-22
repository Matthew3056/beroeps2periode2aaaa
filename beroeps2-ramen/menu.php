<?php
$page_title = "Menu - Ramen Delivery";
require_once 'includes/db.php';
require_once 'includes/header.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}


$search = $_GET['search'] ?? '';
$where_clause = "WHERE available = 1";
$params = [];

if (!empty($search)) {
    $where_clause .= " AND (name LIKE ? OR description LIKE ?)";
    $search_term = "%$search%";
    $params = [$search_term, $search_term];
}

try {
    $stmt = $pdo->prepare("SELECT * FROM dishes $where_clause ORDER BY name");
    $stmt->execute($params);
    $dishes = $stmt->fetchAll();
} catch(PDOException $e) {
    $dishes = [];
}
?>

<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-utensils"></i> Ons Menu</h1>
        <p>Kies uit onze heerlijke Japanse ramen gerechten</p>
    </div>
    
    <div class="search-bar">
        <form method="GET" action="menu.php" class="search-form">
            <div class="search-input-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Zoek gerechten..." 
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <button type="submit" class="btn btn-secondary">
                <i class="fas fa-search"></i> Zoeken
            </button>
        </form>
    </div>
    
    <?php if (empty($dishes)): ?>
        <div class="empty-state">
            <i class="fas fa-search"></i>
            <h2>Geen gerechten gevonden</h2>
            <p>Probeer een andere zoekterm of bekijk later opnieuw.</p>
        </div>
    <?php else: ?>
        <div class="dishes-grid" id="dishesGrid">
            <?php foreach ($dishes as $dish): ?>
                <div class="dish-card" data-dish-id="<?php echo $dish['id']; ?>">
                    <div class="dish-image">
                        <img src="img/<?php echo htmlspecialchars($dish['image']); ?>" 
                             alt="<?php echo htmlspecialchars($dish['name']); ?>"
                             data-fallback="img/default-ramen.jpg">
                    </div>
                    <div class="dish-content">
                        <h3><?php echo htmlspecialchars($dish['name']); ?></h3>
                        <p class="dish-description"><?php echo htmlspecialchars($dish['description']); ?></p>
                        <div class="dish-footer">
                            <span class="dish-price">€<?php echo number_format($dish['price'], 2, ',', '.'); ?></span>
                            <button class="btn btn-primary btn-sm add-to-cart" 
                                    data-dish-id="<?php echo $dish['id']; ?>"
                                    data-dish-name="<?php echo htmlspecialchars($dish['name']); ?>"
                                    data-dish-price="<?php echo $dish['price']; ?>">
                                <i class="fas fa-plus"></i> Toevoegen
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="cart-fab" id="cartFab" style="display: none;">
    <i class="fas fa-shopping-cart"></i>
    <span class="cart-count" id="cartCount">0</span>
</div>

<?php require_once 'includes/footer.php'; ?>
