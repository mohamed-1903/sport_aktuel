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
        parent_id INT DEFAULT NULL,
        stars INT CHECK (stars BETWEEN 1 AND 5),
        display_name VARCHAR(255),
        comment TEXT,
        image_paths TEXT,
        likes INT DEFAULT 0,
        dislikes INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id),
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");

    // Ensure newer columns exist when the table was created previously
    $stmt = $db->query("SHOW COLUMNS FROM ratings LIKE 'image_paths'");
    if ($stmt->rowCount() === 0) {
        $db->exec("ALTER TABLE ratings ADD COLUMN image_paths TEXT AFTER comment");
    }

    $stmt = $db->query("SHOW COLUMNS FROM ratings LIKE 'display_name'");
    if ($stmt->rowCount() === 0) {
        $db->exec("ALTER TABLE ratings ADD COLUMN display_name VARCHAR(255) AFTER user_id");
    }

    $stmt = $db->query("SHOW COLUMNS FROM ratings LIKE 'parent_id'");
    if ($stmt->rowCount() === 0) {
        $db->exec("ALTER TABLE ratings ADD COLUMN parent_id INT DEFAULT NULL AFTER user_id");
    }

    $stmt = $db->query("SHOW COLUMNS FROM ratings LIKE 'likes'");
    if ($stmt->rowCount() === 0) {
        $db->exec("ALTER TABLE ratings ADD COLUMN likes INT DEFAULT 0 AFTER image_paths");
    }

    $stmt = $db->query("SHOW COLUMNS FROM ratings LIKE 'dislikes'");
    if ($stmt->rowCount() === 0) {
        $db->exec("ALTER TABLE ratings ADD COLUMN dislikes INT DEFAULT 0 AFTER likes");
    }
    $done = true;
}

function addRating(int $productId, int $userId, string $displayName, int $stars, string $comment, array $imagePaths, ?int $parentId = null): bool {
    ensureRatingSchema();
    global $db;
    $json = $imagePaths ? json_encode($imagePaths) : null;
    $stmt = $db->prepare("INSERT INTO ratings (product_id, user_id, display_name, stars, comment, image_paths, parent_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$productId, $userId, $displayName, $stars, $comment, $json, $parentId]);
}

function getRatingsForProduct(int $productId): array {
    ensureRatingSchema();
    global $db;
    $stmt = $db->prepare("SELECT r.*, u.username FROM ratings r JOIN users u ON r.user_id = u.id WHERE r.product_id = ? ORDER BY r.created_at ASC");
    $stmt->execute([$productId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $map = [];
    foreach ($rows as $row) {
        if (!empty($row['image_paths'])) {
            $row['image_paths'] = json_decode($row['image_paths'], true) ?: [];
        } elseif (!empty($row['image_path'])) {
            $row['image_paths'] = [$row['image_path']];
        } else {
            $row['image_paths'] = [];
        }
        $row['replies'] = [];
        $map[$row['id']] = $row;
    }

    $top = [];
    foreach ($map as $id => $row) {
        if (!empty($row['parent_id']) && isset($map[$row['parent_id']])) {
            $map[$row['parent_id']]['replies'][] = $row;
        } elseif (empty($row['parent_id'])) {
            $top[] = $row;
        }
    }

    // sort top level by created_at descending
    usort($top, static function ($a, $b) { return strtotime($b['created_at']) <=> strtotime($a['created_at']); });
    return $top;
}

function getAverageRating(int $productId): ?float {
    ensureRatingSchema();
    global $db;
    $stmt = $db->prepare("SELECT AVG(stars) FROM ratings WHERE product_id = ?");
    $stmt->execute([$productId]);
    $avg = $stmt->fetchColumn();
    return $avg ? (float)$avg : null;
}

function deleteRating(int $ratingId, int $userId, bool $isAdmin): bool {
    ensureRatingSchema();
    global $db;
    if ($isAdmin) {
        $stmt = $db->prepare('DELETE FROM ratings WHERE id = ?');
        return $stmt->execute([$ratingId]);
    }
    $stmt = $db->prepare('DELETE FROM ratings WHERE id = ? AND user_id = ?');
    return $stmt->execute([$ratingId, $userId]);
}

function likeRating(int $ratingId): array {
    ensureRatingSchema();
    global $db;
    $db->prepare('UPDATE ratings SET likes = likes + 1 WHERE id = ?')->execute([$ratingId]);
    return getRatingVotes($ratingId);
}

function dislikeRating(int $ratingId): array {
    ensureRatingSchema();
    global $db;
    $db->prepare('UPDATE ratings SET dislikes = dislikes + 1 WHERE id = ?')->execute([$ratingId]);
    return getRatingVotes($ratingId);
}

function unlikeRating(int $ratingId): array {
    ensureRatingSchema();
    global $db;
    $db->prepare('UPDATE ratings SET likes = GREATEST(likes - 1, 0) WHERE id = ?')->execute([$ratingId]);
    return getRatingVotes($ratingId);
}

function undislikeRating(int $ratingId): array {
    ensureRatingSchema();
    global $db;
    $db->prepare('UPDATE ratings SET dislikes = GREATEST(dislikes - 1, 0) WHERE id = ?')->execute([$ratingId]);
    return getRatingVotes($ratingId);
}

function getRatingVotes(int $ratingId): array {
    global $db;
    $stmt = $db->prepare('SELECT likes, dislikes FROM ratings WHERE id = ?');
    $stmt->execute([$ratingId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: ['likes' => 0, 'dislikes' => 0];
}
