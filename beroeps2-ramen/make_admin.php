<?php
/**
 * Script om een gebruiker admin te maken
 * Gebruik dit als je nog geen admin account hebt
 * Verwijder dit bestand na gebruik voor beveiliging
 */

require_once 'includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    
    if (empty($username)) {
        $error = 'Vul een gebruikersnaam in.';
    } else {
        try {

            $stmt = $pdo->prepare("SELECT id, username, role FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if (!$user) {
                $error = 'Gebruiker niet gevonden.';
            } elseif ($user['role'] === 'admin') {
                $error = 'Deze gebruiker is al admin.';
            } else {

                $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE username = ?");
                $stmt->execute([$username]);
                $success = "Gebruiker '$username' is nu admin! Je kunt nu inloggen en het dashboard gebruiken.";
            }
        } catch(PDOException $e) {
            $error = 'Fout bij bijwerken gebruiker: ' . $e->getMessage();
        }
    }
}

// Haal alle gebruikers op
try {
    $stmt = $pdo->query("SELECT username, email, role FROM users ORDER BY username");
    $users = $stmt->fetchAll();
} catch(PDOException $e) {
    $users = [];
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maak Gebruiker Admin - Ramen Delivery</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-header">
                    <i class="fas fa-user-shield"></i>
                    <h1>Maak Gebruiker Admin</h1>
                    <p>Geef een gebruiker adminrechten</p>
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
                        <p style="margin-top: 1rem;"><a href="index.php" class="btn btn-primary">Ga naar Login</a></p>
                    </div>
                <?php else: ?>
                    <form method="POST" action="" class="auth-form">
                        <div class="form-group">
                            <label for="username">
                                <i class="fas fa-user"></i> Gebruikersnaam
                            </label>
                            <input type="text" id="username" name="username" required 
                                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                                   placeholder="Voer gebruikersnaam in">
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-user-shield"></i> Maak Admin
                        </button>
                    </form>
                    
                    <?php if (!empty($users)): ?>
                        <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                            <h3 style="margin-bottom: 1rem; color: var(--text-dark);">Bestaande Gebruikers:</h3>
                            <div style="max-height: 200px; overflow-y: auto;">
                                <?php foreach ($users as $user): ?>
                                    <div style="padding: 0.5rem; background: var(--light-color); margin-bottom: 0.5rem; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;">
                                        <div>
                                            <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                            <span style="color: var(--text-light); font-size: 0.9rem;">
                                                (<?php echo htmlspecialchars($user['email']); ?>)
                                            </span>
                                        </div>
                                        <span class="badge <?php echo $user['role'] === 'admin' ? 'badge-success' : 'badge-secondary'; ?>">
                                            <?php echo $user['role'] === 'admin' ? 'Admin' : 'Gebruiker'; ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                
                <div class="auth-footer">
                    <p><a href="index.php">Terug naar login</a></p>
                    <p style="margin-top: 1rem; font-size: 0.9rem; color: var(--text-light);">
                        <strong>Let op:</strong> Verwijder dit bestand na gebruik voor beveiliging!
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
