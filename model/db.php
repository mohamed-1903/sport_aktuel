<?php
// model/db.php

$host = 'localhost';
$dbname = 'sportx';
$user = 'root';
$pass = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Falls keine Datenbankverbindung möglich ist, wird $db auf null gesetzt.
    // Die Models können dann eine Fallback-Quelle (z. B. JSON-Datei) nutzen.
    $db = null;
}
