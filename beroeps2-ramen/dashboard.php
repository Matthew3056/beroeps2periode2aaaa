<?php
$page_title = "Admin Dashboard - Ramen Delivery";
require_once 'includes/db.php';
require_once 'includes/header.php';

// Check of gebruiker is ingelogd en admin is
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

// Bestelling status updaten
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];

    try {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $order_id]);
        $success = 'Bestelling status bijgewerkt!';
    } catch(PDOException $e) {
        $error = 'Fout bij bijwerken status.';
    }
}

// Gerecht toevoegen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_dish'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $available = isset($_POST['available']) ? 1 : 0;

    // Handle image upload
    $image = 'default-ramen.jpg'; // Default fallback
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/img/';
        $file = $_FILES['image'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];

        // Get file extension
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        // Validate file extension
        if (in_array($fileExt, $allowedExts)) {
            // Validate file size (max 5MB)
            if ($fileSize < 5000000) {
                // Generate unique filename
                $newFileName = uniqid('dish_', true) . '.' . $fileExt;
                $destination = $uploadDir . $newFileName;

                // Create img directory if it doesn't exist
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                // Move uploaded file
                if (move_uploaded_file($fileTmpName, $destination)) {
                    $image = $newFileName;
                } else {
                    $error = 'Fout bij uploaden van afbeelding.';
                }
            } else {
                $error = 'Afbeelding is te groot (maximaal 5MB).';
            }
        } else {
            $error = 'Ongeldig bestandstype. Alleen JPG, PNG, GIF en WEBP zijn toegestaan.';
        }
    } elseif (isset($_POST['image_filename']) && !empty(trim($_POST['image_filename']))) {
        // Fallback: use text input if provided
        $image = trim($_POST['image_filename']);
    }

    if (empty($name) || empty($description) || $price <= 0) {
        $error = 'Vul alle verplichte velden correct in.';
    } elseif (empty($error)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO dishes (name, description, price, image, available) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $price, $image, $available]);
            $success = 'Gerecht toegevoegd!';
        } catch(PDOException $e) {
            $error = 'Fout bij toevoegen gerecht: ' . $e->getMessage();
        }
    }
}

// Gerecht bewerken
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_dish'])) {
    $id = intval($_POST['dish_id']);
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $available = isset($_POST['available']) ? 1 : 0;

    // Get current image from database
    try {
        $stmt = $pdo->prepare("SELECT image FROM dishes WHERE id = ?");
        $stmt->execute([$id]);
        $currentDish = $stmt->fetch();
        $image = $currentDish ? $currentDish['image'] : 'default-ramen.jpg';
    } catch(PDOException $e) {
        $image = 'default-ramen.jpg';
    }

    // Handle image upload if new file is provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/img/';
        $file = $_FILES['image'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];

        // Get file extension
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        // Validate file extension
        if (in_array($fileExt, $allowedExts)) {
            // Validate file size (max 5MB)
            if ($fileSize < 5000000) {
                // Generate unique filename
                $newFileName = uniqid('dish_', true) . '.' . $fileExt;
                $destination = $uploadDir . $newFileName;

                // Create img directory if it doesn't exist
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                // Move uploaded file
                if (move_uploaded_file($fileTmpName, $destination)) {
                    // Delete old image if it's not the default
                    if ($image !== 'default-ramen.jpg' && file_exists($uploadDir . $image)) {
                        @unlink($uploadDir . $image);
                    }
                    $image = $newFileName;
                } else {
                    $error = 'Fout bij uploaden van afbeelding.';
                }
            } else {
                $error = 'Afbeelding is te groot (maximaal 5MB).';
            }
        } else {
            $error = 'Ongeldig bestandstype. Alleen JPG, PNG, GIF en WEBP zijn toegestaan.';
        }
    } elseif (isset($_POST['image_filename']) && !empty(trim($_POST['image_filename']))) {
        // Fallback: use text input if provided and no file upload
        $image = trim($_POST['image_filename']);
    }

    if (empty($error)) {
        try {
            $stmt = $pdo->prepare("UPDATE dishes SET name = ?, description = ?, price = ?, image = ?, available = ? WHERE id = ?");
            $stmt->execute([$name, $description, $price, $image, $available, $id]);
            $success = 'Gerecht bijgewerkt!';
        } catch(PDOException $e) {
            $error = 'Fout bij bijwerken gerecht: ' . $e->getMessage();
        }
    }
}

// Gerecht verwijderen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_dish'])) {
    $id = intval($_POST['dish_id']);

    try {
        $stmt = $pdo->prepare("DELETE FROM dishes WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Gerecht verwijderd!';
    } catch(PDOException $e) {
        $error = 'Fout bij verwijderen gerecht.';
    }
}

// Gebruiker admin maken
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['make_admin'])) {
    $user_id = intval($_POST['user_id']);

    try {
        $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
        $stmt->execute([$user_id]);
        $success = 'Gebruiker is nu admin!';
    } catch(PDOException $e) {
        $error = 'Fout bij toekennen adminrechten.';
    }
}

// Haal data op
try {
    // Bestellingen - gebruik subquery om duplicaten te voorkomen
    $stmt = $pdo->query("
        SELECT o.*, 
               u.username, 
               (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        ORDER BY o.created_at DESC
        LIMIT 20
    ");
    $orders = $stmt->fetchAll();

    // Gerechten
    $stmt = $pdo->query("SELECT * FROM dishes ORDER BY name");
    $dishes = $stmt->fetchAll();

    // Gebruikers
    $stmt = $pdo->query("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
} catch(PDOException $e) {
    $orders = [];
    $dishes = [];
    $users = [];
}
?>

    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
            <p>Beheer bestellingen, gerechten en gebruikers</p>
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

        <div class="dashboard-tabs">
            <button class="tab-btn active" data-tab="orders">
                <i class="fas fa-shopping-cart"></i> Bestellingen
            </button>
            <button class="tab-btn" data-tab="dishes">
                <i class="fas fa-utensils"></i> Gerechten
            </button>
            <button class="tab-btn" data-tab="users">
                <i class="fas fa-users"></i> Gebruikers
            </button>
        </div>

        <!-- Bestellingen Tab -->
        <div class="tab-content active" id="ordersTab">
            <h2>Bestellingen Overzicht</h2>
            <div class="orders-table">
                <?php if (empty($orders)): ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Geen bestellingen gevonden.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card-admin">
                            <div class="order-header">
                                <span class="order-id">Bestelling #<?php echo $order['id']; ?></span>
                                <span class="order-user">Gebruiker: <?php echo htmlspecialchars($order['username']); ?></span>
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
                                <p><i class="fas fa-euro-sign"></i> €<?php echo number_format($order['total'], 2, ',', '.'); ?></p>
                                <p><i class="fas fa-box"></i> <?php echo $order['item_count']; ?> items</p>
                                <p><i class="fas fa-calendar"></i> <?php echo date('d-m-Y H:i', strtotime($order['created_at'])); ?></p>
                            </div>
                            <form method="POST" class="status-form">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="status" class="status-select">
                                    <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>In behandeling</option>
                                    <option value="preparing" <?php echo $order['status'] === 'preparing' ? 'selected' : ''; ?>>Wordt bereid</option>
                                    <option value="delivering" <?php echo $order['status'] === 'delivering' ? 'selected' : ''; ?>>Onderweg</option>
                                    <option value="completed" <?php echo $order['status'] === 'completed' ? 'selected' : ''; ?>>Voltooid</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-save"></i> Update
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Gerechten Tab -->
        <div class="tab-content" id="dishesTab">
            <div class="dishes-admin-section">
                <h2>Gerecht Toevoegen</h2>
                <form method="POST" class="dish-form" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Naam</label>
                            <input type="text" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Prijs (€)</label>
                            <input type="number" name="price" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Beschrijving</label>
                        <textarea name="description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Afbeelding uploaden</label>
                        <input type="file" name="image" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                        <small class="form-help">JPG, PNG, GIF of WEBP (max 5MB). Laat leeg voor standaard afbeelding.</small>
                    </div>
                    <div class="form-group">
                        <label>Of gebruik bestaande bestandsnaam (optioneel)</label>
                        <input type="text" name="image_filename" placeholder="bijv. tonkotsu.jpg">
                        <small class="form-help">Alleen gebruiken als je geen bestand uploadt en een bestaande afbeelding wilt gebruiken.</small>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="available" checked> Beschikbaar
                        </label>
                    </div>
                    <button type="submit" name="add_dish" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Gerecht Toevoegen
                    </button>
                </form>
            </div>

            <h2>Bestaande Gerechten</h2>
            <div class="dishes-grid">
                <?php foreach ($dishes as $dish): ?>
                    <div class="dish-card-admin">
                        <div class="dish-image">
                            <img src="img/<?php echo htmlspecialchars($dish['image']); ?>"
                                 alt="<?php echo htmlspecialchars($dish['name']); ?>"
                                 data-fallback="img/default-ramen.jpg">
                        </div>
                        <div class="dish-content">
                            <h3><?php echo htmlspecialchars($dish['name']); ?></h3>
                            <p><?php echo htmlspecialchars($dish['description']); ?></p>
                            <p class="dish-price">€<?php echo number_format($dish['price'], 2, ',', '.'); ?></p>
                            <p class="dish-available">
                                <?php echo $dish['available'] ? '<span class="badge badge-success">Beschikbaar</span>' : '<span class="badge badge-error">Niet beschikbaar</span>'; ?>
                            </p>
                            <form method="POST" class="dish-edit-form" enctype="multipart/form-data">
                                <input type="hidden" name="dish_id" value="<?php echo $dish['id']; ?>">
                                <div class="form-group">
                                    <label>Naam</label>
                                    <input type="text" name="name" value="<?php echo htmlspecialchars($dish['name']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Beschrijving</label>
                                    <textarea name="description" required><?php echo htmlspecialchars($dish['description']); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Prijs (€)</label>
                                    <input type="number" name="price" step="0.01" value="<?php echo $dish['price']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Huidige afbeelding</label>
                                    <input type="text" name="image_filename" value="<?php echo htmlspecialchars($dish['image']); ?>" readonly>
                                    <small class="form-help">Huidige afbeelding: <?php echo htmlspecialchars($dish['image']); ?></small>
                                </div>
                                <div class="form-group">
                                    <label>Nieuwe afbeelding uploaden (optioneel)</label>
                                    <input type="file" name="image" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                                    <small class="form-help">JPG, PNG, GIF of WEBP (max 5MB). Laat leeg om huidige afbeelding te behouden.</small>
                                </div>
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" name="available" <?php echo $dish['available'] ? 'checked' : ''; ?>> Beschikbaar
                                    </label>
                                </div>
                                <div class="dish-actions">
                                    <button type="submit" name="edit_dish" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-save"></i> Opslaan
                                    </button>
                                    <button type="submit" name="delete_dish" class="btn btn-sm btn-danger delete-dish-btn">
                                        <i class="fas fa-trash"></i> Verwijderen
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Gebruikers Tab -->
        <div class="tab-content" id="usersTab">
            <h2>Gebruikers Overzicht</h2>
            <div class="users-table">
                <?php if (empty($users)): ?>
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <p>Geen gebruikers gevonden.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <div class="user-card">
                            <div class="user-info">
                                <h3><?php echo htmlspecialchars($user['username']); ?></h3>
                                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></p>
                                <p>
                                <span class="badge <?php echo $user['role'] === 'admin' ? 'badge-success' : 'badge-secondary'; ?>">
                                    <?php echo $user['role'] === 'admin' ? 'Admin' : 'Gebruiker'; ?>
                                </span>
                                </p>
                                <p><i class="fas fa-calendar"></i> Lid sinds: <?php echo date('d-m-Y', strtotime($user['created_at'])); ?></p>
                            </div>
                            <?php if ($user['role'] !== 'admin'): ?>
                                <form method="POST">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" name="make_admin" class="btn btn-sm btn-primary">
                                        <i class="fas fa-user-shield"></i> Maak Admin
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

<?php require_once 'includes/footer.php'; ?>