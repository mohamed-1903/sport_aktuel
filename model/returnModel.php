<?php
// model/returnModel.php
require_once 'model/db.php';

function requestReturn(int $orderId, int $userId, string $reason): bool {
    global $db;
    $stmt = $db->prepare("INSERT INTO returns (order_id, user_id, reason, status, created_at) VALUES (?, ?, ?, 'beantragt', NOW())");
    return $stmt->execute([$orderId, $userId, $reason]);
}

function getReturnsByUser(int $userId): array {
    global $db;
    $stmt = $db->prepare("SELECT * FROM returns WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
