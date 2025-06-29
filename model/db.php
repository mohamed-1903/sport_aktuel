<?php
// model/db.php
// Stellt die Verbindung zur Datenbank her

$host = 'localhost';     // Hostname des Datenbankservers
$dbname = 'sportx';      // Name der Datenbank
$user = 'root';          // Benutzername für den Zugriff
$pass = '';              // Passwort für den Zugriff

try {
    // Aufbau der PDO-Verbindung mit UTF-8 Unterstützung
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);

    // Fehler sollen als Exceptions geworfen werden
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Abbruch bei fehlgeschlagener Verbindung
    die("Verbindung zur Datenbank fehlgeschlagen: " . $e->getMessage());
}

// Keine Redundanz festgestellt: Verbindung wird nur einmal aufgebaut
