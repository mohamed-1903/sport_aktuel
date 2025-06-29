<?php
// model/ratingModel.php
require_once 'model/db.php';

/**
 * Ensure that rating tables and required columns exist.
 * Uses a static flag so the schema check runs only once per request.
 */
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

    $db->exec("CREATE TABLE IF NOT EXISTS rating_votes (
        rating_id INT NOT NULL,
        user_id INT NOT NULL,
        vote ENUM('like','dislike') NOT NULL,
        PRIMARY KEY (rating_id, user_id),
        FOREIGN KEY (rating_id) REFERENCES ratings(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    $done = true;
}

/**
 * Insert a new rating for the given product and return the inserted ID.
 */
function addRating(int $productId, int $userId, string $displayName, int $stars, string $comment, array $imagePaths, ?int $parentId = null): int {
    ensureRatingSchema();
    global $db;
    $json = $imagePaths ? json_encode($imagePaths) : null;
    $stmt = $db->prepare("INSERT INTO ratings (product_id, user_id, display_name, stars, comment, image_paths, parent_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$productId, $userId, $displayName, $stars, $comment, $json, $parentId]);
    return (int)$db->lastInsertId();
}

/**
 * Fetch ratings for a product and optionally include the current user's vote.
 * Results are grouped so that replies follow their parent rating.
 */
function getRatingsForProduct(int $productId, ?int $currentUserId = null): array {
    ensureRatingSchema();
    global $db;
    if ($currentUserId) {
        $stmt = $db->prepare(
            "SELECT r.*, u.username, rv.vote AS user_vote
             FROM ratings r
             JOIN users u ON r.user_id = u.id
             LEFT JOIN rating_votes rv ON rv.rating_id = r.id AND rv.user_id = ?
             WHERE r.product_id = ? ORDER BY r.created_at ASC"
        );
        $stmt->execute([$currentUserId, $productId]);
    } else {
        $stmt = $db->prepare(
            "SELECT r.*, u.username FROM ratings r
             JOIN users u ON r.user_id = u.id
             WHERE r.product_id = ? ORDER BY r.created_at ASC"
        );
        $stmt->execute([$productId]);
    }
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $names = [];
    $comments = [];
    foreach ($rows as &$row) {
        if (!empty($row['image_paths'])) {
            $row['image_paths'] = json_decode($row['image_paths'], true) ?: [];
        } elseif (!empty($row['image_path'])) {
            $row['image_paths'] = [$row['image_path']];
        } else {
            $row['image_paths'] = [];
        }
        $names[$row['id']] = $row['display_name'] ?: $row['username'];
        $comments[$row['id']] = $row['comment'];
    }
    unset($row);

    foreach ($rows as &$row) {
        $row['parent_name'] = null;
        $row['parent_comment'] = null;
        if (!empty($row['parent_id']) && isset($names[$row['parent_id']])) {
            $row['parent_name'] = $names[$row['parent_id']];
            $snippet = $comments[$row['parent_id']] ?? '';
            $row['parent_comment'] = mb_strimwidth($snippet, 0, 60, '…');
        }
    }
    unset($row);

    $grouped = [];
    foreach ($rows as $row) {
        $parent = $row['parent_id'] ? (int)$row['parent_id'] : 0;
        $grouped[$parent][] = $row;
    }

    $ordered = [];
    $add = static function (array $list, &$ordered, &$grouped) use (&$add) {
        usort($list, static fn($a, $b) => strtotime($a['created_at']) <=> strtotime($b['created_at']));
        foreach ($list as $item) {
            $ordered[] = $item;
            $pid = (int)$item['id'];
            if (!empty($grouped[$pid])) {
                $add($grouped[$pid], $ordered, $grouped);
            }
        }
    };

    $roots = $grouped[0] ?? [];
    $add($roots, $ordered, $grouped);

    return $ordered;

}

/**
 * Calculate the average star rating for a product.
 */
function getAverageRating(int $productId): ?float {
    ensureRatingSchema();
    global $db;
    $stmt = $db->prepare("SELECT AVG(stars) FROM ratings WHERE product_id = ?");
    $stmt->execute([$productId]);
    $avg = $stmt->fetchColumn();
    return $avg ? (float)$avg : null;
}

/**
 * Delete a rating and any uploaded images. The SQL used for admin and user
 * deletion is almost identical and could be consolidated.
 */
function deleteRating(int $ratingId, int $userId, bool $isAdmin): bool {
    ensureRatingSchema();
    global $db;

    if ($isAdmin) {
        $stmt = $db->prepare('SELECT image_paths FROM ratings WHERE id = ?');
        $stmt->execute([$ratingId]);
    } else {
        $stmt = $db->prepare('SELECT image_paths FROM ratings WHERE id = ? AND user_id = ?');
        $stmt->execute([$ratingId, $userId]);
    }
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        return false;
    }

    $paths = [];
    if (!empty($row['image_paths'])) {
        $decoded = json_decode($row['image_paths'], true);
        $paths = $decoded && is_array($decoded) ? $decoded : [$row['image_paths']];
    }

    if ($isAdmin) {
        $stmt = $db->prepare('DELETE FROM ratings WHERE id = ?');
        $success = $stmt->execute([$ratingId]);
    } else {
        $stmt = $db->prepare('DELETE FROM ratings WHERE id = ? AND user_id = ?');
        $success = $stmt->execute([$ratingId, $userId]);
    }

    if ($success) {
        foreach ($paths as $path) {
            if ($path && is_file($path)) {
                $realBase = realpath('uploads');
                $realPath = realpath($path);
                if ($realBase !== false && $realPath !== false && strpos($realPath, $realBase) === 0) {
                    @unlink($realPath);
                }
            }
        }
    }

    return $success;
}

/**
 * Register a like for a rating and return updated vote counts.
 * Shares much of its logic with dislikeRating().
 */
function likeRating(int $ratingId, int $userId): array {
    ensureRatingSchema();
    global $db;
    $db->beginTransaction();
    $stmt = $db->prepare('SELECT vote FROM rating_votes WHERE rating_id = ? AND user_id = ?');
    $stmt->execute([$ratingId, $userId]);
    $existing = $stmt->fetchColumn();
    if ($existing === 'dislike') {
        $db->prepare('UPDATE ratings SET dislikes = GREATEST(dislikes - 1, 0) WHERE id = ?')->execute([$ratingId]);
        $db->prepare('UPDATE rating_votes SET vote = "like" WHERE rating_id = ? AND user_id = ?')->execute([$ratingId, $userId]);
        $db->prepare('UPDATE ratings SET likes = likes + 1 WHERE id = ?')->execute([$ratingId]);
    } elseif ($existing !== 'like') {
        $db->prepare('INSERT INTO rating_votes (rating_id, user_id, vote) VALUES (?, ?, "like")')->execute([$ratingId, $userId]);
        $db->prepare('UPDATE ratings SET likes = likes + 1 WHERE id = ?')->execute([$ratingId]);
    }
    $db->commit();
    $votes = getRatingVotes($ratingId);
    $votes['user_vote'] = 'like';
    return $votes;
}

/**
 * Register a dislike for a rating. Essentially the counterpart to likeRating().
 */
function dislikeRating(int $ratingId, int $userId): array {
    ensureRatingSchema();
    global $db;
    $db->beginTransaction();
    $stmt = $db->prepare('SELECT vote FROM rating_votes WHERE rating_id = ? AND user_id = ?');
    $stmt->execute([$ratingId, $userId]);
    $existing = $stmt->fetchColumn();
    if ($existing === 'like') {
        $db->prepare('UPDATE ratings SET likes = GREATEST(likes - 1, 0) WHERE id = ?')->execute([$ratingId]);
        $db->prepare('UPDATE rating_votes SET vote = "dislike" WHERE rating_id = ? AND user_id = ?')->execute([$ratingId, $userId]);
        $db->prepare('UPDATE ratings SET dislikes = dislikes + 1 WHERE id = ?')->execute([$ratingId]);
    } elseif ($existing !== 'dislike') {
        $db->prepare('INSERT INTO rating_votes (rating_id, user_id, vote) VALUES (?, ?, "dislike")')->execute([$ratingId, $userId]);
        $db->prepare('UPDATE ratings SET dislikes = dislikes + 1 WHERE id = ?')->execute([$ratingId]);
    }
    $db->commit();
    $votes = getRatingVotes($ratingId);
    $votes['user_vote'] = 'dislike';
    return $votes;
}

/**
 * Remove a like from a rating for the given user.
 */
function unlikeRating(int $ratingId, int $userId): array {
    ensureRatingSchema();
    global $db;
    $db->beginTransaction();
    $stmt = $db->prepare('DELETE FROM rating_votes WHERE rating_id = ? AND user_id = ? AND vote = "like"');
    $stmt->execute([$ratingId, $userId]);
    if ($stmt->rowCount() > 0) {
        $db->prepare('UPDATE ratings SET likes = GREATEST(likes - 1, 0) WHERE id = ?')->execute([$ratingId]);
    }
    $db->commit();
    $votes = getRatingVotes($ratingId);
    $votes['user_vote'] = null;
    return $votes;
}

/**
 * Remove a dislike from a rating for the given user.
 */
function undislikeRating(int $ratingId, int $userId): array {
    ensureRatingSchema();
    global $db;
    $db->beginTransaction();
    $stmt = $db->prepare('DELETE FROM rating_votes WHERE rating_id = ? AND user_id = ? AND vote = "dislike"');
    $stmt->execute([$ratingId, $userId]);
    if ($stmt->rowCount() > 0) {
        $db->prepare('UPDATE ratings SET dislikes = GREATEST(dislikes - 1, 0) WHERE id = ?')->execute([$ratingId]);
    }
    $db->commit();
    $votes = getRatingVotes($ratingId);
    $votes['user_vote'] = null;
    return $votes;
}

/**
 * Return the like and dislike counts for a rating.
 */
function getRatingVotes(int $ratingId): array {
    global $db;
    $stmt = $db->prepare('SELECT likes, dislikes FROM ratings WHERE id = ?');
    $stmt->execute([$ratingId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: ['likes' => 0, 'dislikes' => 0];
}

/**
 * Retrieve a single rating along with parent and user information.
 * Contains duplicated query logic depending on whether a current user is given.
 */
function getRating(int $ratingId, ?int $currentUserId = null): ?array {
    ensureRatingSchema();
    global $db;
    if ($currentUserId) {
        $stmt = $db->prepare(
            'SELECT r.*, u.username, rv.vote AS user_vote,
                    p.display_name AS parent_display_name, pu.username AS parent_username, p.comment AS parent_comment
             FROM ratings r
             JOIN users u ON r.user_id = u.id
             LEFT JOIN rating_votes rv ON rv.rating_id = r.id AND rv.user_id = ?
             LEFT JOIN ratings p ON r.parent_id = p.id
             LEFT JOIN users pu ON p.user_id = pu.id
             WHERE r.id = ?'
        );
        $stmt->execute([$currentUserId, $ratingId]);
    } else {
        $stmt = $db->prepare(
            'SELECT r.*, u.username,
                    p.display_name AS parent_display_name, pu.username AS parent_username, p.comment AS parent_comment
             FROM ratings r
             JOIN users u ON r.user_id = u.id
             LEFT JOIN ratings p ON r.parent_id = p.id
             LEFT JOIN users pu ON p.user_id = pu.id
             WHERE r.id = ?'
        );

        $stmt->execute([$ratingId]);
    }
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        return null;
    }
    if (!empty($row['image_paths'])) {
        $row['image_paths'] = json_decode($row['image_paths'], true) ?: [];
    } else {
        $row['image_paths'] = [];
    }
    $row['parent_name'] = null;
    $row['parent_comment'] = null;
    if (!empty($row['parent_id'])) {
        $row['parent_name'] = $row['parent_display_name'] ?: $row['parent_username'];
        if (!empty($row['parent_comment'])) {
            $row['parent_comment'] = mb_strimwidth($row['parent_comment'], 0, 60, '…');
        }
    }
    unset($row['parent_display_name'], $row['parent_username']);
    return $row;
}

