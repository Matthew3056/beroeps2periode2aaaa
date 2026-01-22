<?php
// SQLite Database connectie
// Veel eenvoudiger dan MySQL - geen server nodig, gewoon een bestand!

$db_file = __DIR__ . '/../database/ramen_delivery.db';

try {
    // Maak database directory aan als deze niet bestaat
    $db_dir = dirname($db_file);
    if (!is_dir($db_dir)) {
        mkdir($db_dir, 0755, true);
    }
    
    // Maak connectie met SQLite database
    $pdo = new PDO("sqlite:$db_file");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Schakel foreign keys in (standaard uit in SQLite)
    $pdo->exec("PRAGMA foreign_keys = ON");
    
    // Maak tabellen aan als ze niet bestaan (alleen bij eerste keer)
    // Check eerst of users tabel bestaat om te voorkomen dat we elke keer initialiseren
    $table_check = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'");
    if ($table_check->fetch() === false) {
        initDatabase($pdo);
    }
    
} catch(PDOException $e) {
    die("
    <div style='padding: 20px; font-family: Arial; max-width: 600px; margin: 50px auto; background: #f8d7da; border: 2px solid #dc3545; border-radius: 8px;'>
        <h2 style='color: #721c24;'>Database Connectie Fout</h2>
        <p><strong>Fout:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
        <p>Controleer of de map <code>database/</code> schrijfrechten heeft.</p>
    </div>
    ");
}

/**
 * Initialiseer database tabellen als ze niet bestaan
 */
function initDatabase($pdo) {
    try {
        // Tabel: users
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            role TEXT NOT NULL DEFAULT 'user' CHECK(role IN ('user', 'admin')),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Tabel: dishes
        $pdo->exec("CREATE TABLE IF NOT EXISTS dishes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            description TEXT,
            price REAL NOT NULL,
            image TEXT DEFAULT 'default-ramen.jpg',
            available INTEGER DEFAULT 1 CHECK(available IN (0, 1))
        )");
        
        // Tabel: orders
        $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            total REAL NOT NULL,
            status TEXT NOT NULL DEFAULT 'pending' CHECK(status IN ('pending', 'preparing', 'delivering', 'completed')),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )");
        
        // Tabel: order_items
        $pdo->exec("CREATE TABLE IF NOT EXISTS order_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_id INTEGER NOT NULL,
            dish_id INTEGER NOT NULL,
            quantity INTEGER NOT NULL DEFAULT 1,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            FOREIGN KEY (dish_id) REFERENCES dishes(id) ON DELETE CASCADE
        )");
        
        // Check of er al data is, zo niet: voeg testdata toe
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        
        if ($result && $result['count'] == 0) {
            // Insert test admin gebruiker (wachtwoord: admin123)
            $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'admin')");
            $stmt->execute(['admin', $admin_password, 'admin@ramendelivery.nl']);
            
            // Insert test gerechten
            $dishes = [
                ['Tonkotsu Ramen', 'Rijke varkensbouillon met malse varkensvlees, zacht gekookt ei, nori en groene uien', 12.50, 'tonkotsu.jpg', 1],
                ['Shoyu Ramen', 'Klassieke sojasaus bouillon met kip, bamboescheuten, nori en gekookt ei', 11.00, 'shoyu.jpg', 1],
                ['Miso Ramen', 'Hartige miso bouillon met varkensvlees, maïs, boter, nori en groene uien', 11.50, 'miso.jpg', 1],
                ['Spicy Ramen', 'Pittige bouillon met varkensvlees, gekookt ei, nori en extra chili', 12.00, 'spicy.jpg', 1],
                ['Vegetarian Ramen', 'Groentebouillon met tofu, champignons, zeewier en groene uien', 10.50, 'vegetarian.jpg', 1],
                ['Chicken Ramen', 'Lichte kippenbouillon met gegrilde kip, groene uien en gekookt ei', 11.00, 'chicken.jpg', 1]
            ];
            
            $stmt = $pdo->prepare("INSERT INTO dishes (name, description, price, image, available) VALUES (?, ?, ?, ?, ?)");
            foreach ($dishes as $dish) {
                $stmt->execute($dish);
            }
        }
    } catch(PDOException $e) {
        // Silent fail - database bestaat mogelijk al
        error_log("Database init error: " . $e->getMessage());
    }
}
?>
