<?php
require_once 'model/db.php'; // Stellt $db (PDO) bereit

function addToCart(int $userId, array $item): void
{
    global $db;

    // Prüfen ob Eintrag schon existiert
    $stmt = $db->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ? AND size = ?");
    $stmt->execute([$userId, $item['id'], $item['size']]);
    $existing = $stmt->fetch();

    if ($existing) {
        // Menge aktualisieren
        $newQty = $existing['quantity'] + $item['quantity'];
        $update = $db->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
        $update->execute([$newQty, $existing['id']]);
    } else {
        // Neues Produkt einfügen
        $insert = $db->prepare("INSERT INTO cart_items (user_id, product_id, size, quantity) VALUES (?, ?, ?, ?)");
        $insert->execute([$userId, $item['id'], $item['size'], $item['quantity']]);
    }
}

function getCartItems(int $userId): array
{
    global $db;

    $stmt = $db->prepare("
        SELECT c.product_id, c.size, c.quantity, 
               p.name, p.price, p.image_main
        FROM cart_items c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function removeFromCart(int $userId, int $productId, string $size): void
{
    global $db;

    $stmt = $db->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ? AND size = ?");
    $stmt->execute([$userId, $productId, $size]);
}

function updateCartQuantity(int $userId, int $productId, string $size, int $quantity): void
{
    global $db;

    $stmt = $db->prepare("UPDATE cart_items SET quantity = ? WHERE user_id = ? AND product_id = ? AND size = ?");
    $stmt->execute([$quantity, $userId, $productId, $size]);
}

function clearCart(int $userId): void
{
    global $db;

    $stmt = $db->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $stmt->execute([$userId]);
}



function countCartItems(int $userId): int
{
    global $db;

    $stmt = $db->prepare("SELECT SUM(quantity) FROM cart_items WHERE user_id = ?");
    $stmt->execute([$userId]);
    return (int) $stmt->fetchColumn();
}
function isInCart(int $userId, int $productId, string $size): bool
{
    global $db;

    $stmt = $db->prepare("SELECT 1 FROM cart_items WHERE user_id = ? AND product_id = ? AND size = ? LIMIT 1");
    $stmt->execute([$userId, $productId, $size]);

    return (bool) $stmt->fetchColumn();
}
