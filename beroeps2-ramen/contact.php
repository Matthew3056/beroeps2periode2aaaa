<?php
$page_title = "Contact - Ramen Delivery";
require_once 'includes/db.php';
require_once 'includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'Vul alle velden in.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Ongeldig e-mailadres.';
    } else {

        $success = 'Bedankt voor je bericht! We nemen zo snel mogelijk contact met je op.';
        
        // Optioneel: Opslaan in database (als je een contact_messages tabel hebt)
        // try {
        //     $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, datetime('now'))");
        //     $stmt->execute([$name, $email, $subject, $message]);
        // } catch(PDOException $e) {
        //     // Silent fail - bericht wordt niet opgeslagen maar gebruiker ziet wel succesmelding
        // }
    }
}
?>

<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-envelope"></i> Contact</h1>
        <p>Heb je een vraag? Neem contact met ons op!</p>
    </div>
    
    <div class="contact-section">
        <div class="contact-info">
            <h2>Contactgegevens</h2>
            
            <div class="contact-item">
                <div class="contact-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="contact-details">
                    <h3>Adres</h3>
                    <p>Ramenstraat 123<br>1234 AB Amsterdam</p>
                </div>
            </div>
            
            <div class="contact-item">
                <div class="contact-icon">
                    <i class="fas fa-phone"></i>
                </div>
                <div class="contact-details">
                    <h3>Telefoon</h3>
                    <p>020-1234567</p>
                    <p class="contact-hours">Ma-Zo: 11:00 - 22:00</p>
                </div>
            </div>
            
            <div class="contact-item">
                <div class="contact-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="contact-details">
                    <h3>E-mail</h3>
                    <p><a href="mailto:info@ramendelivery.nl">info@ramendelivery.nl</a></p>
                </div>
            </div>
            
            <div class="contact-item">
                <div class="contact-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="contact-details">
                    <h3>Openingstijden</h3>
                    <p>Maandag - Zondag<br>11:00 - 22:00</p>
                </div>
            </div>
        </div>
        
        <div class="contact-form-wrapper">
            <h2>Stuur ons een bericht</h2>
            
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
            <?php else: ?>
                <form method="POST" action="" class="contact-form">
                    <div class="form-group">
                        <label for="name">
                            <i class="fas fa-user"></i> Naam
                        </label>
                        <input type="text" id="name" name="name" required 
                               value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i> E-mailadres
                        </label>
                        <input type="email" id="email" name="email" required 
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">
                            <i class="fas fa-tag"></i> Onderwerp
                        </label>
                        <input type="text" id="subject" name="subject" required 
                               value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="message">
                            <i class="fas fa-comment"></i> Bericht
                        </label>
                        <textarea id="message" name="message" rows="6" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-paper-plane"></i> Verstuur Bericht
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="contact-map">
        <h2>Onze Locatie</h2>
        <div class="map-placeholder">
            <i class="fas fa-map-marked-alt"></i>
            <p>Kaart wordt hier getoond</p>
            <p class="map-note">(Je kunt hier een Google Maps embed toevoegen)</p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
