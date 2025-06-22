<?php
// model/watchlistModel.php
require_once 'model/db.php';

function ensureWatchlistSchema(): void {
    global $db;
    static $done = false;
    if ($done) {
        return;
    }
    $db->exec("CREATE TABLE IF NOT EXISTS watchlists (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
    )");
    $db->exec("CREATE TABLE IF NOT EXISTS watchlist_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        watchlist_id INT,
        product_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (watchlist_id) REFERENCES watchlists (id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
    )");
    $done = true;
}

function getWatchlistId(int $userId, bool $create = false): ?int {
    ensureWatchlistSchema();
    global $db;
    $stmt = $db->prepare('SELECT id FROM watchlists WHERE user_id = ? LIMIT 1');
    $stmt->execute([$userId]);
    $id = $stmt->fetchColumn();
    if (!$id && $create) {
        $ins = $db->prepare('INSERT INTO watchlists (user_id) VALUES (?)');
        $ins->execute([$userId]);
        return (int)$db->lastInsertId();
    }
    return $id ? (int)$id : null;
}

function ensureWatchlist(int $userId): int {
    return getWatchlistId($userId, true);
}

function addToWatchlist(int $userId, int $productId): void {
    global $db;
    $listId = ensureWatchlist($userId);
    $stmt = $db->prepare('SELECT id FROM watchlist_items WHERE watchlist_id = ? AND product_id = ?');
    $stmt->execute([$listId, $productId]);
    if ($stmt->fetch()) {
        return;
    }
    $insert = $db->prepare('INSERT INTO watchlist_items (watchlist_id, product_id) VALUES (?, ?)');
    $insert->execute([$listId, $productId]);
}

function getWatchlistItems(int $userId): array {
    global $db;
    $stmt = $db->prepare(
        'SELECT wi.product_id, p.name, p.price, p.image_main
         FROM watchlist_items wi
         JOIN watchlists w ON wi.watchlist_id = w.id
         JOIN products p ON wi.product_id = p.id
         WHERE w.user_id = ?'
    );
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function removeFromWatchlist(int $userId, int $productId): void {
    global $db;
    $listId = getWatchlistId($userId);
    if (!$listId) {
        return;
    }
    $stmt = $db->prepare('DELETE FROM watchlist_items WHERE watchlist_id = ? AND product_id = ?');
    $stmt->execute([$listId, $productId]);
}

function clearWatchlist(int $userId): void {
    global $db;
    $listId = getWatchlistId($userId);
    if (!$listId) {
        return;
    }
    $db->prepare('DELETE FROM watchlist_items WHERE watchlist_id = ?')->execute([$listId]);
}

function countWatchlistItems(int $userId): int {
    global $db;
    $stmt = $db->prepare(
        'SELECT COUNT(*)
         FROM watchlist_items wi
         JOIN watchlists w ON wi.watchlist_id = w.id
         WHERE w.user_id = ?'
    );
    $stmt->execute([$userId]);
    return (int)$stmt->fetchColumn();
}

function isInWatchlist(int $userId, int $productId): bool {
    global $db;
    $stmt = $db->prepare(
        'SELECT 1
         FROM watchlist_items wi
         JOIN watchlists w ON wi.watchlist_id = w.id
         WHERE w.user_id = ? AND wi.product_id = ?
         LIMIT 1'
    );
    $stmt->execute([$userId, $productId]);
    return (bool)$stmt->fetchColumn();
}

function setWatchlistItems(int $userId, array $productIds): void {
    clearWatchlist($userId);
    foreach ($productIds as $pid) {
        addToWatchlist($userId, (int)$pid);
    }
}

function toggleWatchlistBulk(int $userId, array $productIds): array {
    $results = [];
    foreach ($productIds as $pid) {
        $pid = (int)$pid;
        if (isInWatchlist($userId, $pid)) {
            removeFromWatchlist($userId, $pid);
            $results[] = ['id' => $pid, 'in_watchlist' => false];
        } else {
            addToWatchlist($userId, $pid);
            $results[] = ['id' => $pid, 'in_watchlist' => true];
        }
    }
    return $results;
}
