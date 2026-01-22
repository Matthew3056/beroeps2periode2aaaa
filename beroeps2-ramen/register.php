<?php
$page_title = "Registreren - Ramen Delivery";
require_once 'includes/db.php';
require_once 'includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    if (empty($username) || empty($email) || empty($password) || empty($password_confirm)) {
        $error = 'Vul alle velden in.';
    } elseif ($password !== $password_confirm) {
        $error = 'Wachtwoorden komen niet overeen.';
    } elseif (strlen($password) < 6) {
        $error = 'Wachtwoord moet minimaal 6 tekens lang zijn.';
    } else {
        try {
            // Check of username al bestaat
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                $error = 'Gebruikersnaam of e-mailadres bestaat al.';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$username, $email, $hashed_password]);
                $success = 'Registratie succesvol! Je kunt nu inloggen.';
            }
        } catch(PDOException $e) {
            $error = 'Er is een fout opgetreden. Probeer het later opnieuw.';
        }
    }
}
?>

<div class="container">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <i class="fas fa-bowl-rice"></i>
                <h1>Account aanmaken</h1>
                <p>Registreer om te bestellen</p>
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
                    <p><a href="index.php">Klik hier om in te loggen</a></p>
                </div>
            <?php else: ?>
                <form method="POST" action="" class="auth-form">
                    <div class="form-group">
                        <label for="username">
                            <i class="fas fa-user"></i> Gebruikersnaam
                        </label>
                        <input type="text" id="username" name="username" required 
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i> E-mailadres
                        </label>
                        <input type="email" id="email" name="email" required 
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock"></i> Wachtwoord
                        </label>
                        <input type="password" id="password" name="password" required 
                               minlength="6">
                    </div>
                    
                    <div class="form-group">
                        <label for="password_confirm">
                            <i class="fas fa-lock"></i> Bevestig wachtwoord
                        </label>
                        <input type="password" id="password_confirm" name="password_confirm" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-user-plus"></i> Registreren
                    </button>
                </form>
            <?php endif; ?>
            
            <div class="auth-footer">
                <p>Al een account? <a href="index.php">Log hier in</a></p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
