<?php
require_once 'model/db.php'; // Stellt $db (PDO) bereit

/**
 * Gibt die ID des Warenkorbs für einen Nutzer zurück.
 * Existiert keiner, wird optional einer angelegt.
 */
function getCartId(int $userId, bool $create = false): ?int
{
    global $db;

    $stmt = $db->prepare('SELECT id FROM cart WHERE user_id = ? LIMIT 1');
    $stmt->execute([$userId]);
    $cartId = $stmt->fetchColumn();

    if (!$cartId && $create) {
        $insert = $db->prepare('INSERT INTO cart (user_id) VALUES (?)');
        $insert->execute([$userId]);
        return (int)$db->lastInsertId();
    }

    return $cartId ? (int)$cartId : null;
}

function ensureCart(int $userId): int
{
    return getCartId($userId, true);
}

function addToCart(int $userId, array $item): void
{
    global $db;

    $cartId = ensureCart($userId);

    // Prüfen ob Eintrag schon existiert
    $stmt = $db->prepare("SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ? AND size = ?");
    $stmt->execute([$cartId, $item['id'], $item['size']]);
    $existing = $stmt->fetch();

    if ($existing) {
        // Menge aktualisieren
        $newQty = $existing['quantity'] + $item['quantity'];
        $update = $db->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
        $update->execute([$newQty, $existing['id']]);
    } else {
        // Neues Produkt einfügen␊
        $insert = $db->prepare("INSERT INTO cart_items (cart_id, product_id, size, quantity) VALUES (?, ?, ?, ?)");
        $insert->execute([$cartId, $item['id'], $item['size'], $item['quantity']]);
    }
}

function getCartItems(int $userId): array
{
    global $db;

    $stmt = $db->prepare(
        "SELECT ci.id AS cart_item_id,
                ci.product_id,
                ci.size,
                ci.quantity,
                p.name,
                p.price,
                p.image_main
         FROM cart_items ci
         JOIN cart c ON ci.cart_id = c.id
         JOIN products p ON ci.product_id = p.id
         WHERE c.user_id = ?"
    );
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function removeFromCart(int $userId, int $productId, string $size): void
{
    global $db;

    $cartId = getCartId($userId);
    if ($cartId === null) {
        return;
    }

    $stmt = $db->prepare("DELETE FROM cart_items WHERE cart_id = ? AND product_id = ? AND size = ?");
    $stmt->execute([$cartId, $productId, $size]);
}

function updateCartQuantity(int $userId, int $productId, string $size, int $quantity): void
{
    global $db;

    $cartId = getCartId($userId);
    if ($cartId === null) {
        return;
    }

    $stmt = $db->prepare("UPDATE cart_items SET quantity = ? WHERE cart_id = ? AND product_id = ? AND size = ?");
    $stmt->execute([$quantity, $cartId, $productId, $size]);
}

function clearCart(int $userId): void
{
    global $db;

    $cartId = getCartId($userId);
    if ($cartId === null) {
        return;
    }

    $stmt = $db->prepare("DELETE FROM cart_items WHERE cart_id = ?");
    $stmt->execute([$cartId]);
}



function countCartItems(int $userId): int
{
    global $db;

    $stmt = $db->prepare(
        "SELECT SUM(ci.quantity)
         FROM cart_items ci
         JOIN cart c ON ci.cart_id = c.id
         WHERE c.user_id = ?"
    );
    $stmt->execute([$userId]);
    return (int) $stmt->fetchColumn();
}
function isInCart(int $userId, int $productId, string $size): bool
{
    global $db;

    $stmt = $db->prepare(
        "SELECT 1
         FROM cart_items ci
         JOIN cart c ON ci.cart_id = c.id
         WHERE c.user_id = ? AND ci.product_id = ? AND ci.size = ?
         LIMIT 1"
    );
    $stmt->execute([$userId, $productId, $size]);

    return (bool) $stmt->fetchColumn();
}