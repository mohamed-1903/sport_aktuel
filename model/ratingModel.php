<?php
require_once 'model/db.php';

function ensureRatingSchema(): void {
    global $db;
    static $done = false;
    if ($done) return;
    $db->exec("CREATE TABLE IF NOT EXISTS ratings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT,
        user_id INT,
        stars INT CHECK (stars BETWEEN 1 AND 5),
        comment TEXT,
        image_path VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id),
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");
    $done = true;
}

function addRating(int $productId, int $userId, int $stars, string $comment, ?string $imagePath): bool {
    ensureRatingSchema();
    global $db;
    $stmt = $db->prepare("INSERT INTO ratings (product_id, user_id, stars, comment, image_path) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([$productId, $userId, $stars, $comment, $imagePath]);
}

function getRatingsForProduct(int $productId): array {
    ensureRatingSchema();
    global $db;
    $stmt = $db->prepare("SELECT r.*, u.username FROM ratings r JOIN users u ON r.user_id = u.id WHERE r.product_id = ? ORDER BY r.created_at DESC");
    $stmt->execute([$productId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAverageRating(int $productId): ?float {
    ensureRatingSchema();
    global $db;
    $stmt = $db->prepare("SELECT AVG(stars) FROM ratings WHERE product_id = ?");
    $stmt->execute([$productId]);
    $avg = $stmt->fetchColumn();
    return $avg ? (float)$avg : null;
}
