<?php

// index.php – Frontcontroller

session_start(); // Startet eine neue oder bestehende Session. Wichtig für Login, Warenkorb etc.

error_reporting(E_ALL); // Aktiviert die Anzeige aller Fehlermeldungen (nur für Entwicklung empfohlen).
ini_set("display_errors", 1); // Sorgt dafür, dass Fehler direkt im Browser angezeigt werden.

// require_once 'config.php'; // Lädt die Konfigurationsdatei (z.B. DB-Zugangsdaten, Konstanten).

$page = $_GET['page'] ?? 'static'; // Liest den 'page'-Parameter aus der URL (?page=...), Standardwert ist 'static', falls nicht gesetzt.
$action = $_GET['action'] ?? 'index'; // Liest den 'action'-Parameter aus der URL (?action=...), Standardwert ist 'index'.

// Routing: Controller laden basierend auf 'page'
switch ($page) {
    case 'product':
        require_once 'controller/productController.php';
        break;

    case 'auth':
        require_once 'controller/authController.php';
        break;

    case 'cart':
        require_once 'controller/cartController.php';
        break;

    case 'watchlist':
        require_once 'controller/watchlistController.php';
        break;

    case 'admin':
        require_once 'controller/adminController.php';
        break;

    case 'order':
        require_once 'controller/orderController.php';
        break;

    case 'community':
        require_once 'controller/communityController.php';
        break;
case 'user':
  require_once 'controller/userController.php';
  break;

    case 'static':
        $staticAction = $_GET['action'] ?? 'index';
        switch ($staticAction) {
            case 'about':
                require 'view/static/aboutView.php';
                break;
            case 'impressum':
                require 'view/static/impressumView.php';
                break;
            case 'datenschutz':
                require 'view/static/datenschutzView.php';
                break;
            case 'kontakt':
                require 'view/static/kontaktView.php';
                break;
            default:
                require 'view/static/indexView.php';
        }
        break;

    default:
        // Fallback auf statische Willkommensseite
        require_once 'view/static/indexView.php';
        break;
}
