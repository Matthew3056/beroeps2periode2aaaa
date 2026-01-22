<?php
/**
 * Script om admin gebruiker aan te maken
 * Voer dit script één keer uit na database installatie
 * Verwijder dit bestand daarna voor beveiliging
 */

require_once 'includes/db.php';

$username = 'admin';
$password = 'admin123';
$email = 'admin@ramendelivery.nl';


$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR role = 'admin'");
$stmt->execute([$username]);
if ($stmt->fetch()) {
    echo "Admin gebruiker bestaat al!<br>";
    echo "<a href='index.php'>Ga naar login</a>";
    exit;
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'admin')");
$stmt->execute([$username, $hashed_password, $email]);

echo "Admin gebruiker succesvol aangemaakt!<br>";
echo "Gebruikersnaam: $username<br>";
echo "Wachtwoord: $password<br>";
echo "<br><strong>BELANGRIJK: Verwijder dit bestand (create_admin.php) na gebruik!</strong><br>";
echo "<a href='index.php'>Ga naar login</a>";
