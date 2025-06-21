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

function clearCart(): void {
    $_SESSION['cart'] = [];
}