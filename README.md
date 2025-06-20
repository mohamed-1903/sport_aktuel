todo:
css für datenschutz und impressum, kontakt
filter zurücksetzten button


kommentier jede zeile mit einer klärung und warum genau so

/sportx
│
├── controller/
│   ├── authController.php              # Login, Registrierung, Logout
│   ├── productController.php           # Produkte anzeigen, Detailansicht
│   ├── cartController.php              # Warenkorb-Funktionen
│   ├── adminController.php             # Adminfunktionen
│   ├── orderController.php             # Bestellung verwalten
│   └── communityController.php         # Fan-Storys, Bewertungen, Umfragen
│
├── model/
│   ├── db.php                          # DB-Verbindung
│   ├── userModel.php                   # Benutzerlogik
│   ├── productModel.php                # Produktlogik
│   ├── cartModel.php                   # Warenkorbstruktur
│   ├── orderModel.php                  # Bestellungen
│   └── ratingModel.php                 # Bewertungen und Kommentare
│
├── view/
│   ├── layout/
│   │   ├── header.php
│   │   └── footer.php
│   ├── auth/
│   │   ├── loginView.php
│   │   ├── registerView.php
│   │   └── logoutView.php
│   ├── product/
│   │   ├── productListView.php
│   │   ├── productDetailView.php
│   │   └── compareView.php
│   ├── user/
│   │   └── userProfileView.php
│   ├── cart/
│   │   ├── cartView.php
│   │   ├── wishlistView.php
│   │   └── summaryView.php
│   ├── admin/
│   │   ├── adminDashboardView.php
│   │   ├── manageUsersView.php
│   │   ├── manageOrdersView.php
│   │   └── addProductView.php
│   ├── static/
│   │   ├── indexView.php
│   │   └── aboutView.php
│   ├── community/
│   │   ├── fanStoriesView.php
│   │   ├── ratingView.php
│   │   └── pollVotingView.php
│   └── special/
│       ├── lookBuilderView.php
│       ├── productQuizView.php
│       ├── promoCountdownView.php
│       └── productStoryView.php
│
├── css/
│   ├── mystyle.css
│   └── animations.css
│
├── js/
│   ├── validation.js
│   ├── cart.js
│   ├── darkmode.js
│   ├── layoutSwitch.js
│   ├── search.js
│   ├── chatbot.js
│   ├── lookbuilder.js
│   ├── rating.js
│   └── analytics.js
│
├── data/
│   ├── products.json
│   ├── ratings.json
│   └── recommendations.json
│
├── images/
│   └── (Produktbilder)
│
├── uploads/
│   └── (Benutzer-Bilder oder dynamische Uploads)
│
├── assets/
│   └── icons/
│       └── (SVG/PNG-Icons für UI)
│
├── index.php                          # Frontcontroller, verarbeitet page=...&action=...
├── config.php                         # Datenbankverbindung, App-Konstanten
├── .htaccess                          # RewriteEngine für clean URLs (optional)
└── README.md                          # Dokumentation des Projekts


Klar! Ich erkläre dir jetzt **Schritt für Schritt**, **wie die Dateien zusammenspielen** – anhand des **MVC-Musters (Model-View-Controller)** in deiner Struktur:

---

## 🔁 **1. Einstiegspunkt: `index.php` (Frontcontroller)**

### Funktion:

* Verarbeitet URL-Parameter wie `?page=product&action=detail&id=123`
* Entscheidet, **welcher Controller** zuständig ist

### Beispiel:

```php
// index.php
$page = $_GET['page'] ?? 'static';
$action = $_GET['action'] ?? 'index';

switch ($page) {
    case 'product':
        require_once 'controller/productController.php';
        break;
    case 'auth':
        require_once 'controller/authController.php';
        break;
    case 'admin':
        require_once 'controller/adminController.php';
        break;
    // usw.
}
```

---

## 🧠 **2. Controller (z. B. `productController.php`)**

### Funktion:

* Logik für den aktuellen Request
* Holt Daten aus dem Model
* Ruft passende View auf

### Beispiel:

```php
// controller/productController.php
require_once 'model/productModel.php';

if ($action === 'detail') {
    $id = $_GET['id'];
    $product = getProductById($id); // aus productModel.php
    require 'view/product/productDetailView.php'; // zeigt es an
}
```

---

## 📦 **3. Model (z. B. `productModel.php`)**

### Funktion:

* Holt oder speichert Daten in der **Datenbank**
* Liefert die Daten an den Controller

### Beispiel:

```php
// model/productModel.php
function getProductById($id) {
    global $db; // kommt aus db.php
    $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}
```

---

## 🖼️ **4. View (z. B. `productDetailView.php`)**

### Funktion:

* Präsentiert Daten im HTML-Layout
* Nutzt ggf. `header.php` und `footer.php`

### Beispiel:

```php
// view/product/productDetailView.php
include 'view/layout/header.php';
?>
<h1><?= htmlspecialchars($product['name']) ?></h1>
<p><?= htmlspecialchars($product['description']) ?></p>
<img src="images/<?= $product['image'] ?>" />
<?php
include 'view/layout/footer.php';
```

---

## 🧩 **5. Gemeinsame Dateien**

* `config.php`: enthält z. B. DB-Zugangsdaten, `define('BASE_URL', ...)`
* `db.php`: baut Verbindung zur Datenbank auf (wird in `model/*.php` eingebunden)
* `layout/header.php` und `footer.php`: Basisstruktur (HTML `<head>`, Navigation, etc.)

---

## 🔐 **Beispiel für Admin-Feature-Aufruf**

```plaintext
http://localhost/sportx/index.php?page=admin&action=addProduct
```

1. `index.php` ruft `adminController.php` auf
2. Dort: `addProduct()` verarbeitet POST-Daten und ruft `productModel::addProduct()`
3. Danach Weiterleitung oder `require view/admin/addProductView.php`

---

## ↩️ **Neue Retouren-Funktion**

- Controller: `returnController.php`
- Model: `returnModel.php`
- Views: `view/return/returnFormView.php`, `view/return/returnSuccessView.php`
- Aufrufbeispiel: `index.php?page=return&action=form&order_id=123`

---

Wenn du möchtest, kann ich diesen Ablauf visuell als Diagramm darstellen oder dir eine Beispielroute komplett mit Code zeigen. Sag einfach, was du brauchst!
