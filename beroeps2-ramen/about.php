<?php
$page_title = "Over Ons - Ramen Delivery";
require_once 'includes/db.php';
require_once 'includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-bowl-rice"></i> Over Ramen Delivery</h1>
        <p>Authentieke Japanse ramen, direct bij jou bezorgd</p>
    </div>
    
    <div class="about-section">
        <div class="about-card">
            <div class="about-icon">
                <i class="fas fa-utensils"></i>
            </div>
            <h2>Onze Missie</h2>
            <p>Bij Ramen Delivery brengen we de authentieke smaak van Japanse ramen direct bij jou thuis. Onze passie voor traditionele Japanse keuken combineert met moderne bezorgservice, zodat je kunt genieten van heerlijke, verse ramen zonder je huis te verlaten.</p>
        </div>
        
        <div class="about-card">
            <div class="about-icon">
                <i class="fas fa-seedling"></i>
            </div>
            <h2>Verse Ingrediënten</h2>
            <p>We gebruiken alleen de beste en meest verse ingrediënten. Onze bouillons worden dagelijks vers bereid volgens traditionele Japanse recepten, en we selecteren zorgvuldig elk ingrediënt voor de perfecte smaakbeleving.</p>
        </div>
        
        <div class="about-card">
            <div class="about-icon">
                <i class="fas fa-truck"></i>
            </div>
            <h2>Snel Bezorgd</h2>
            <p>Onze bezorgservice is snel en betrouwbaar. Bestel je favoriete ramen en geniet binnen 30-45 minuten van een heerlijke maaltijd. We zorgen ervoor dat je ramen perfect warm en vers aankomt.</p>
        </div>
        
        <div class="about-card">
            <div class="about-icon">
                <i class="fas fa-heart"></i>
            </div>
            <h2>Met Liefde Bereid</h2>
            <p>Elke kom ramen wordt met zorg en aandacht bereid door onze ervaren chefs. We geloven dat goed eten niet alleen gaat om smaak, maar ook om de liefde en passie die erin gestopt wordt.</p>
        </div>
    </div>
    
    <div class="about-story">
        <h2>Ons Verhaal</h2>
        <p>Ramen Delivery is ontstaan uit een passie voor authentieke Japanse ramen. Onze oprichter ontdekte tijdens een reis door Japan de diepe, rijke smaken van traditionele ramen en wilde deze ervaring delen met iedereen in Nederland.</p>
        <p>Wat begon als een kleine droom, is uitgegroeid tot een bloeiende bezorgservice die dagelijks honderden tevreden klanten bedient. We blijven trouw aan de traditionele recepten terwijl we innoveren met moderne bezorgtechnologie.</p>
    </div>
    
    <div class="about-values">
        <h2>Onze Waarden</h2>
        <div class="values-grid">
            <div class="value-item">
                <i class="fas fa-award"></i>
                <h3>Kwaliteit</h3>
                <p>We accepteren alleen het beste</p>
            </div>
            <div class="value-item">
                <i class="fas fa-users"></i>
                <h3>Klantgericht</h3>
                <p>Jouw tevredenheid is onze prioriteit</p>
            </div>
            <div class="value-item">
                <i class="fas fa-leaf"></i>
                <h3>Duurzaam</h3>
                <p>Milieuvriendelijke verpakkingen</p>
            </div>
            <div class="value-item">
                <i class="fas fa-clock"></i>
                <h3>Betrouwbaar</h3>
                <p>Altijd op tijd, altijd vers</p>
            </div>
        </div>
    </div>
    
    <div class="about-cta">
        <h2>Klaar om te bestellen?</h2>
        <p>Ontdek onze heerlijke selectie van authentieke Japanse ramen</p>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="menu.php" class="btn btn-primary">
                <i class="fas fa-utensils"></i> Bekijk Menu
            </a>
        <?php else: ?>
            <a href="register.php" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Maak Account
            </a>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
