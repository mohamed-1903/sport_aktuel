<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportX</title>
    <link rel="icon" type="image/png" href="img/logo.png">
    <link rel="stylesheet" href="css/mystyle.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800&display=swap" rel="stylesheet">
</head>
<body>
<header>
    <div class="header-bar">
        <a href="index.php" class="logo" title="zur Startseite">&#127942; SportX</a>
        <div class="search-wrapper">
            <input type="text" id="autocomplete-shadow" readonly tabindex="-1" />
            <input type="text" id="produktsuche" placeholder="🔍 Suche nach Produkten..." autocomplete="off" />
            <ul id="such-vorschlaege" class="autocomplete-liste"></ul>
        </div>
        <div class="action-buttons">
               <a href="index.php?page=watchlist&action=view" title="Favoriten">
                <button type="button" id="watchlist-button">&#10084;</button>
            </a>
            <div class="dropdown-konto">
                <button type="button" aria-haspopup="true" aria-expanded="false">👤</button>
                <div class="konto-popup">
                    <?php if (empty($_SESSION['user_id'])): ?>
                        <a href="index.php?page=auth&action=login" class="btn-primary">Anmelden</a>
                        <p>oder <a href="index.php?page=auth&action=register">registrieren</a></p>
                    <?php else: ?>
                        <p>Hallo, <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?> 👋</p>
                        <?php if (!empty($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                            <a href="index.php?page=admin&action=dashboard">Admin-Dashboard</a>
                            <a href="index.php?page=admin&action=manageUsers">Benutzer verwalten</a>
                            <a href="index.php?page=admin&action=addProduct">Produkt hinzufügen</a>
                        <?php else: ?>
                            <a href="index.php?page=user&action=profile">Mein Profil</a>
                            <a href="index.php?page=user&action=orders">Bestellungen</a>
                        <?php endif; ?>
                        <a href="index.php?page=auth&action=logout">Abmelden</a>
                    <?php endif; ?>
                    <hr>
                    <a href="#">Passwort vergessen?</a>
                    <a href="index.php?page=return&action=form">Retoure anmelden?</a>
                </div>
            </div>
            <a href="index.php?page=cart&action=view" title="Warenkorb">
                <button type="button" id="cart-button">&#128722;</button>
            </a>
            <button id="theme-toggle" title="Design wechseln">&#127769;</button>
        </div>
    </div>
    <div id="toast-popup" class="toast-popup"></div>
    <div id="cart-preview-popup" class="cart-preview-popup"></div>
    <div id="cart-popup" class="cart-preview-popup"></div>
    <div id="watchlist-preview-popup" class="watchlist-preview-popup"></div>
    <div id="watch-popup" class="watchlist-preview-popup"></div>
    <nav>
        <ul class="nav-menu">
            <li class="dropdown">
                <a href="index.php?page=product&action=list&category=Sportbekleidung">Sportbekleidung</a>
                <ul class="dropdown-menu">
                    <li><a href="index.php?page=product&action=list&category=Sportbekleidung&subcategory=Trikots">Trikots</a></li>
                    <li><a href="index.php?page=product&action=list&category=Sportbekleidung&subcategory=Socken">Socken</a></li>
                    <li><a href="index.php?page=product&action=list&category=Sportbekleidung&subcategory=Handschuhe">Handschuhe</a></li>
                    <li><a href="index.php?page=product&action=list&category=Sportbekleidung&subcategory=Trainingsanzüge">Trainingsanzüge</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="index.php?page=product&action=list&category=Fußballschuhe">Fußballschuhe</a>
                <ul class="dropdown-menu">
                    <li><a href="index.php?page=product&action=list&category=Fußballschuhe&subcategory=Stollen">Stollen</a></li>
                    <li><a href="index.php?page=product&action=list&category=Fußballschuhe&subcategory=Kunstrasen">Kunstrasen</a></li>
                    <li><a href="index.php?page=product&action=list&category=Fußballschuhe&subcategory=Hallenschuhe">Hallenschuhe</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="index.php?page=product&action=list&category=Zubehör">Zubehör</a>
                <ul class="dropdown-menu">
                    <li><a href="index.php?page=product&action=list&category=Zubehör&subcategory=Schienbeinschoner">Schienbeinschoner</a></li>
                    <li><a href="index.php?page=product&action=list&category=Zubehör&subcategory=Fußbälle">Fußbälle</a></li>
                    <li><a href="index.php?page=product&action=list&category=Zubehör&subcategory=Sporttaschen">Sporttaschen</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#">Sale %</a>
                <ul class="dropdown-menu">
                    <li><a href="index.php?page=product&action=list&category=Sportbekleidung&sale=1">Sportbekleidung</a></li>
                    <li><a href="index.php?page=product&action=list&category=Fußballschuhe&sale=1">Fußballschuhe</a></li>
                    <li><a href="index.php?page=product&action=list&category=Zubehör&sale=1">Zubehör</a></li>
                </ul>
            </li>
        </ul>
    </nav>
</header>

