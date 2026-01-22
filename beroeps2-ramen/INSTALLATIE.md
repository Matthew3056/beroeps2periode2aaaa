# Installatie Instructies - Ramen Delivery

## Stap 1: Database Setup

1. Open phpMyAdmin in je browser
2. Maak een nieuwe database aan met de naam `ramen_delivery`
3. Selecteer de database
4. Ga naar het tabblad "SQL"
5. Kopieer en plak de inhoud van `database.sql` en voer het uit
6. Of importeer het `database.sql` bestand direct

## Stap 2: Database Configuratie

Open `includes/db.php` en pas indien nodig aan:
```php
$host = 'localhost';        // Meestal localhost
$dbname = 'ramen_delivery'; // Database naam
$username = 'root';         // Je MySQL gebruikersnaam
$password = '';             // Je MySQL wachtwoord
```

## Stap 3: Admin Account Aanmaken

**Optie A: Via create_admin.php (Aanbevolen)**
1. Open in je browser: `http://localhost/beroeps2-ramen/create_admin.php`
2. Het script maakt automatisch een admin account aan
3. **VERWIJDER** daarna het bestand `create_admin.php` voor beveiliging!

**Optie B: Handmatig via phpMyAdmin**
1. Ga naar de `users` tabel
2. Voeg een nieuwe gebruiker toe met:
   - username: `admin`
   - password: (gebruik een password hash generator of het create_admin.php script)
   - email: `admin@ramendelivery.nl`
   - role: `admin`

## Stap 4: Afbeeldingen Toevoegen

Plaats afbeeldingen in de `img/` map:
- `tonkotsu.jpg`
- `shoyu.jpg`
- `miso.jpg`
- `spicy.jpg`
- `vegetarian.jpg`
- `chicken.jpg`
- `default-ramen.jpg` (fallback afbeelding)

Als je geen afbeeldingen hebt, gebruik dan placeholder afbeeldingen of laat de default-ramen.jpg gebruiken.

## Stap 5: Test de Applicatie

1. Open `http://localhost/beroeps2-ramen/` in je browser
2. Log in met:
   - Gebruikersnaam: `admin`
   - Wachtwoord: `admin123`
3. Test de functionaliteit:
   - Registreer een nieuwe gebruiker
   - Bekijk het menu
   - Voeg gerechten toe aan de winkelwagen
   - Plaats een bestelling
   - Test het admin dashboard

## Problemen Oplossen

### Database connectie fout
- Controleer of MySQL/MariaDB draait
- Controleer gebruikersnaam en wachtwoord in `includes/db.php`
- Controleer of de database `ramen_delivery` bestaat

### Sessie problemen
- Zorg dat PHP sessions werken
- Controleer PHP error logs

### Afbeeldingen worden niet getoond
- Controleer of de `img/` map bestaat
- Controleer bestandsnamen (hoofdlettergevoelig!)
- Voeg een `default-ramen.jpg` toe als fallback

## Veiligheid

Na installatie:
1. ✅ Verwijder `create_admin.php`
2. ✅ Wijzig het admin wachtwoord
3. ✅ Controleer `.htaccess` instellingen
4. ✅ Zet error reporting uit in productie (in `includes/db.php`)

## Klaar!

Je applicatie is nu klaar voor gebruik. Veel succes met je schoolproject! 🍜
