<?php
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

    global $db;

    try {
        $stmt = $db->query("SHOW COLUMNS FROM cart_items LIKE 'custom_name'");
        $supported = (bool) $stmt->fetch();
    } catch (PDOException $e) {
        $supported = false;
    }

    return $supported;
}

/**
 * Prüft einmalig, ob die optionalen Spalten für Trikot‑Personalisierung
 * in der Tabelle cart_items vorhanden sind.
 */
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

    $gift = !empty($item['gift']) ? 1 : 0;
    $discount = isset($item['discount']) ? (int)$item['discount'] : 0;

    if (customizationSupported()) {
        $customName = $item['custom_name'] ?? null;
        $customNumber = $item['custom_number'] ?? null;
        $customFee = $item['custom_fee'] ?? 0;

        // Prüfen ob Eintrag schon existiert (inkl. Personalisierung)
        $stmt = $db->prepare(
            "SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ? AND size = ? AND custom_name <=> ? AND custom_number <=> ? AND custom_fee = ?"
        );
        $stmt->execute([$cartId, $item['id'], $item['size'], $customName, $customNumber, $customFee]);
        $existing = $stmt->fetch();

        if ($existing) {
            $newQty = $existing['quantity'] + $item['quantity'];
            $update = $db->prepare(
                "UPDATE cart_items SET quantity = ?, discount = ?, gift = ?, custom_name = ?, custom_number = ?, custom_fee = ? WHERE id = ?"
            );
            $update->execute([$newQty, $discount, $gift, $customName, $customNumber, $customFee, $existing['id']]);
            return;
        }

        $insert = $db->prepare(
            "INSERT INTO cart_items (cart_id, product_id, size, quantity, discount, gift, custom_name, custom_number, custom_fee) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $insert->execute([$cartId, $item['id'], $item['size'], $item['quantity'], $discount, $gift, $customName, $customNumber, $customFee]);
    } else {
        // Prüfen ob Eintrag schon existiert (ohne Personalisierung)
        $stmt = $db->prepare(
            "SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ? AND size = ?"
        );
        $stmt->execute([$cartId, $item['id'], $item['size']]);
        $existing = $stmt->fetch();

        if ($existing) {
            $newQty = $existing['quantity'] + $item['quantity'];
            $update = $db->prepare("UPDATE cart_items SET quantity = ?, discount = ?, gift = ? WHERE id = ?");
            $update->execute([$newQty, $discount, $gift, $existing['id']]);
        } else {
            $insert = $db->prepare(
                "INSERT INTO cart_items (cart_id, product_id, size, quantity, discount, gift) VALUES (?, ?, ?, ?, ?, ?)"
            );
            $insert->execute([$cartId, $item['id'], $item['size'], $item['quantity'], $discount, $gift]);
        }
    }
}

function getCartItems(int $userId): array
{
    global $db;

    if (customizationSupported()) {
        $select = "SELECT ci.id AS cart_item_id,
                        ci.product_id,
                        ci.size,
                        ci.quantity,
                        ci.discount,
                        ci.gift,
                        ci.custom_name,
                        ci.custom_number,
                        ci.custom_fee,
                        p.name,
                        p.price,
                        p.image_main
                 FROM cart_items ci
                 JOIN cart c ON ci.cart_id = c.id
                 JOIN products p ON ci.product_id = p.id
                 WHERE c.user_id = ?";
    } else {
        $select = "SELECT ci.id AS cart_item_id,
                        ci.product_id,
                        ci.size,
                        ci.quantity,
                        ci.discount,
                        ci.gift,
                        p.name,
                        p.price,
                        p.image_main
                 FROM cart_items ci
                 JOIN cart c ON ci.cart_id = c.id
                 JOIN products p ON ci.product_id = p.id
                 WHERE c.user_id = ?";
    }

    $stmt = $db->prepare($select);
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
