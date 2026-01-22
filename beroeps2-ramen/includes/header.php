<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Ramen Delivery'; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
          crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        /* Loading indicator - verbergt na laden */
        .page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.3s;
        }
        .page-loader.hidden {
            opacity: 0;
            pointer-events: none;
        }
        .loader-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid var(--border-color);
            border-top-color: var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="page-loader" id="pageLoader">
        <div class="loader-spinner"></div>
    </div>
    <nav class="navbar">
        <div class="container">
            <div class="nav-content">
                <a href="<?php echo $is_logged_in ? 'menu.php' : 'index.php'; ?>" class="logo">
                    <i class="fas fa-bowl-rice"></i>
                    <span>Ramen Delivery</span>
                </a>
                <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Menu">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="nav-links" id="navLinks">
                    <a href="about.php"><i class="fas fa-info-circle"></i> <span>Over Ons</span></a>
                    <a href="contact.php"><i class="fas fa-envelope"></i> <span>Contact</span></a>
                    <?php if ($is_logged_in): ?>
                        <a href="menu.php"><i class="fas fa-utensils"></i> <span>Menu</span></a>
                        <a href="order.php"><i class="fas fa-shopping-cart"></i> <span>Bestelling</span></a>
                        <?php if ($is_admin): ?>
                            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
                        <?php endif; ?>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Uitloggen</span></a>
                    <?php else: ?>
                        <a href="index.php"><i class="fas fa-sign-in-alt"></i> <span>Inloggen</span></a>
                        <a href="register.php"><i class="fas fa-user-plus"></i> <span>Registreren</span></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <main class="main-content">
