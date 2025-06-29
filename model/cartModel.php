<?php
require_once 'model/db.php'; // Stellt $db (PDO) bereit

/**
 * Prüft einmalig, ob die optionalen Spalten für Trikot‑Personalisierung
 * in der Tabelle cart_items vorhanden sind.
 */
if (!function_exists('customizationSupported')) {
    function customizationSupported(): bool
    {
        static $supported;
        if ($supported !== null) {
            return $supported;
        }

        global $db;
        try {
            $stmt = $db->query("SHOW COLUMNS FROM cart_items LIKE 'custom_name'");
            $supported = (bool) $stmt->fetch();
        } catch (PDOException $e) {
            $supported = false;
        }

        return $supported;
    }
}


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
    $stmt->execute([$userId]);

    return $cartId ? (int)$cartId : null;
}
// Erstellt falls nötig einen Warenkorb für den Nutzer und gibt dessen ID zurück.

function ensureCart(int $userId): int
{
    return getCartId($userId, true);
}

// Fügt einen Artikel zum Warenkorb hinzu oder erhöht die Menge, falls er bereits vorhanden ist.
function addToCart(int $userId, array $item): void
{
    global $db;

    $cartId = ensureCart($userId);

    // Prüfen ob Eintrag schon existiert
    $gift = !empty($item['gift']) ? 1 : 0;
    $discount = isset($item['discount']) ? (int)$item['discount'] : 0;
    $customFee = isset($item['custom_fee']) ? (float)$item['custom_fee'] : 0;

    $discountCode = discountCodeSupported() ? ($item['discount_code'] ?? null) : null;

    if (discountCodeSupported()) {
        $stmt = $db->prepare(
            "SELECT id, quantity FROM cart_items
             WHERE cart_id = ? AND product_id = ? AND size = ? AND discount = ? AND discount_code <=> ? AND gift = ?
               AND custom_name <=> ? AND custom_number <=> ? AND custom_fee = ?"
        );
        $stmt->execute([
            $cartId,
            $item['id'],
            $item['size'],
            $discount,
            $discountCode,
            $gift,
            $item['custom_name'] ?? null,
            $item['custom_number'] ?? null,
            $customFee,
        ]);
    } else {
        $stmt = $db->prepare(
            "SELECT id, quantity FROM cart_items
             WHERE cart_id = ? AND product_id = ? AND size = ? AND discount = ? AND gift = ?
               AND custom_name <=> ? AND custom_number <=> ? AND custom_fee = ?"
        );
        $stmt->execute([
            $cartId,
            $item['id'],
            $item['size'],
            $discount,
            $gift,
            $item['custom_name'] ?? null,
            $item['custom_number'] ?? null,
            $customFee,
        ]);
    }
    $existing = $stmt->fetch();

    if ($existing) {
        $newQty = $existing['quantity'] + $item['quantity'];
        if (discountCodeSupported()) {
            $update = $db->prepare("UPDATE cart_items SET quantity = ?, discount = ?, discount_code = ?, gift = ?, custom_name = ?, custom_number = ?, custom_fee = ? WHERE id = ?");
            $update->execute([$newQty, $discount, $discountCode, $gift, $item['custom_name'] ?? null, $item['custom_number'] ?? null, $customFee, $existing['id']]);
        } else {
            $update = $db->prepare("UPDATE cart_items SET quantity = ?, discount = ?, gift = ?, custom_name = ?, custom_number = ?, custom_fee = ? WHERE id = ?");
            $update->execute([$newQty, $discount, $gift, $item['custom_name'] ?? null, $item['custom_number'] ?? null, $customFee, $existing['id']]);
        }
    } else {
        if (discountCodeSupported()) {
            $insert = $db->prepare("INSERT INTO cart_items (cart_id, product_id, size, quantity, discount, discount_code, gift, custom_name, custom_number, custom_fee) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insert->execute([$cartId, $item['id'], $item['size'], $item['quantity'], $discount, $discountCode, $gift, $item['custom_name'] ?? null, $item['custom_number'] ?? null, $customFee]);
        } else {
            $insert = $db->prepare("INSERT INTO cart_items (cart_id, product_id, size, quantity, discount, gift, custom_name, custom_number, custom_fee) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insert->execute([$cartId, $item['id'], $item['size'], $item['quantity'], $discount, $gift, $item['custom_name'] ?? null, $item['custom_number'] ?? null, $customFee]);
        }
    }
}

/**
 * Prüft einmalig, ob die Spalte discount_code existiert.
 */
if (!function_exists('discountCodeSupported')) {
    function discountCodeSupported(): bool
    {
        static $supported;
        if ($supported !== null) {
            return $supported;
        }

        global $db;
        try {
            $stmt = $db->query("SHOW COLUMNS FROM cart_items LIKE 'discount_code'");
            $supported = (bool) $stmt->fetch();
        } catch (PDOException $e) {
            $supported = false;
        }

        return $supported;
    }
}


// Gibt alle Warenkorb-Positionen eines Nutzers samt Produktdaten zurück.
function getCartItems(int $userId): array
{
    global $db;

    $base = "SELECT ci.id AS cart_item_id,
                    ci.product_id,
                    ci.size,
                    ci.quantity,
                    ci.discount";
    if (discountCodeSupported()) {
        $base .= ", ci.discount_code";
    }
    $base .= ",
                    ci.gift";

    if (customizationSupported()) {
        $base .= ",
                    ci.custom_name,
                    ci.custom_number,
                    ci.custom_fee";
    }

    $select = $base . ",
                    p.name,
                    p.price,
                    p.image_main
             FROM cart_items ci
             JOIN cart c ON ci.cart_id = c.id
             JOIN products p ON ci.product_id = p.id
             WHERE c.user_id = ?";




    $stmt = $db->prepare($select);
    $stmt->execute([$userId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Entfernt einen Artikel mit bestimmter Größe aus dem Warenkorb.
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

// Aktualisiert die Menge eines Artikels im Warenkorb.
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

// Löscht alle Artikel aus dem Warenkorb des Nutzers.
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



// Zählt die Gesamtanzahl der Artikel im Warenkorb des Nutzers.
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
// Prüft, ob ein bestimmter Artikel im Warenkorb enthalten ist.
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
