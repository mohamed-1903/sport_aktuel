Webtech SportX

SportX ist ein webbasiertes E-Commerce-Projekt für den Verkauf von Sportartikeln. Die Anwendung wurde im Rahmen eines Webtechnologie-Kurses entwickelt und beinhaltet grundlegende Funktionen eines Onlineshops: Produktsuche, Warenkorb, Benutzerverwaltung und Adminbereich.
 Funktionen

Benutzerregistrierung und Login
Produktübersicht und Detailansicht
Warenkorb hinzufügen, entfernen, bearbeiten
Bestellabwicklung
Admin-Dashboard zur Verwaltung von Nutzern und Bestellungen
Dynamische Views mit Layout-Switch und Darkmode
Clientseitige Formularvalidierung (JS)

Projektstruktur
```plaintext
/controller/         # PHP-Controller für Logik und Routing
/model/              # Datenmodelle und DB-Anbindung
/view/               # HTML/PHP-Views für verschiedene Seiten
/css/                # Eigene Stylesheets
/js/                 # Clientseitige Logik (Validation, UI)
/data/               # Produktdaten im JSON-Format
/images/             # Produktbilder
/uploads/            # Hochgeladene Dateien
index.php            # Frontcontroller mit Routing über GET-Parameter
README.md            # Diese Datei