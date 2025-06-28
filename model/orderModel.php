<?php
// model/orderModel.php
require_once 'model/db.php';

function saveOrder(int $userId, array $cartItems): bool {
    global $db;

    $orderData = json_encode($cartItems);

    $stmt = $db->prepare("INSERT INTO orders (user_id, status, admin_comment, created_at) VALUES (?, 'neu', '', NOW())");
    if ($stmt->execute([$userId])) {
        $orderId = $db->lastInsertId();

        $stmtData = $db->prepare("UPDATE orders SET admin_comment = ? WHERE id = ?");
        return $stmtData->execute([$orderData, $orderId]);
    }

    return false;
}
function cancelOrderIfNew(int $orderId, int $userId): void {
    global $db;

    $stmt = $db->prepare("
        UPDATE orders 
        SET status = 'storniert', updated_at = NOW() 
        WHERE id = ? AND user_id = ? AND status = 'neu'
    ");
    $stmt->execute([$orderId, $userId]);
}


function getOrdersByUser(int $userId): array {
    global $db;
    $stmt = $db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getAllOrders(): array {
    global $db;
    $stmt = $db->query("SELECT * FROM orders ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getOrdersByStatus(string $status): array {
    global $db;
    $stmt = $db->prepare("SELECT * FROM orders WHERE status = ? ORDER BY created_at DESC");
    $stmt->execute([$status]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getOrderById(int $orderId): ?array {
    global $db;
    $stmt = $db->prepare("SELECT * FROM orders WHERE id = ? LIMIT 1");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    return $order ?: null;
}

function updateOrderStatus(int $orderId, string $status, string $reason = null): bool {
    global $db;
    if ($status === 'abgelehnt') {
        $stmt = $db->prepare("UPDATE orders SET status = ?, rejection_reason = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$status, $reason, $orderId]);
    }

    $stmt = $db->prepare("UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?");
    return $stmt->execute([$status, $orderId]);
}

function cancelOrder(int $orderId, int $userId): bool {
    global $db;
    $stmt = $db->prepare("UPDATE orders SET status = 'storniert', updated_at = NOW() WHERE id = ? AND user_id = ? AND status = 'neu'");
    $stmt->execute([$orderId, $userId]);
    return $stmt->rowCount() > 0;
}

function countOrdersByUser(int $userId): int {
    global $db;
    $stmt = $db->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND status != 'storniert'");
    $stmt->execute([$userId]);
    return (int)$stmt->fetchColumn();
}
