# Ramen Delivery - Schoolproject Bezorgapp

Een volledige PHP/SQLite bezorgapp met Japanse ramen-thema, gebouwd met mobile-first design.

## Features

- ✅ Gebruikersregistratie en authenticatie
- ✅ Dynamisch menu met gerechten uit database
- ✅ Winkelwagen functionaliteit (localStorage)
- ✅ Bestellingen plaatsen en volgen
- ✅ Admin dashboard voor bestellingenbeheer
- ✅ Gerechtenbeheer (toevoegen, bewerken, verwijderen)
- ✅ Gebruikersbeheer (adminrechten toekennen)
- ✅ Mobile-first responsive design
- ✅ Japanse ramen-thema met moderne UI
- ✅ Zoekfunctie voor gerechten
- ✅ Live bestelstatus updates

## Installatie

### 1. Database Setup

**Geen setup nodig!** De database wordt automatisch aangemaakt bij het eerste gebruik.

SQLite gebruikt een bestand (`database/ramen_delivery.db`) in plaats van een server. Dit betekent:
- ✅ Geen MySQL/MariaDB server nodig
- ✅ Geen phpMyAdmin nodig
- ✅ Automatische database aanmaak
- ✅ Testdata wordt automatisch toegevoegd

### 2. Afbeeldingen

Plaats afbeeldingen van ramen-gerechten in de `img/` map. De gerechten in de database verwijzen naar:
- `tonkotsu.jpg`
- `shoyu.jpg`
- `miso.jpg`
- `spicy.jpg`
- `vegetarian.jpg`
- `chicken.jpg`
- `default-ramen.jpg` (fallback)

### 3. Webserver

Zet de bestanden in je webserver directory (bijv. `htdocs` of `www`) en open de applicatie in je browser.

## Standaard Accounts

**Admin Account:**
- Gebruikersnaam: `admin`
- Wachtwoord: `admin123`

**Let op:** Wijzig dit wachtwoord direct na installatie!

## Projectstructuur

```
/project
├── index.php          # Login pagina
├── register.php       # Registratie pagina
├── logout.php         # Uitloggen
├── menu.php           # Menu met gerechten
├── order.php          # Bestellingen pagina
├── dashboard.php      # Admin dashboard
├── database/          # SQLite database bestand (wordt automatisch aangemaakt)
├── database.sql       # Database schema (referentie)
├── css/
│   └── style.css      # Styling
├── js/
│   └── main.js        # JavaScript functionaliteit
├── img/               # Afbeeldingen
└── includes/
    ├── db.php         # Database connectie
    ├── header.php     # Header template
    └── footer.php     # Footer template
```

## Technologieën

- **Backend:** PHP 7.4+ met PDO
- **Database:** SQLite (geen server nodig!)
- **Frontend:** HTML5, CSS3, JavaScript (ES6+)
- **Styling:** Custom CSS met CSS Variables
- **Icons:** Font Awesome 6.4.0
- **Fonts:** Google Fonts (Roboto, Noto Sans JP)

## Beveiliging

- ✅ Prepared statements (SQL injection preventie)
- ✅ Password hashing met `password_hash()`
- ✅ Session management
- ✅ Input sanitization met `htmlspecialchars()`
- ✅ Admin-only pagina's beveiligd
- ✅ CSRF protection (kan toegevoegd worden)

## Browser Ondersteuning

- Chrome (laatste versies)
- Firefox (laatste versies)
- Safari (laatste versies)
- Edge (laatste versies)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Licentie

Dit is een schoolproject voor educatieve doeleinden.

## Auteur

Schoolproject - Ramen Delivery App
