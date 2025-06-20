<?php include 'view/layout/header.php'; ?>

<div class="watchlist-wrapper">
    <h2>Meine Favoriten</h2>
    <section id="watchlist-container">
        <?php if (!empty($watchlistItems)): ?>
            <?php foreach ($watchlistItems as $item): ?>
                <div class="watchlist-card">
                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                    <h3><?= htmlspecialchars($item['name']) ?></h3>
                    <p><?= number_format($item['price'], 2) ?>€</p>
                    <div class="button-group">
                        <a href="index.php?page=product&action=detail&id=<?= (int)$item['id'] ?>"><button>Details</button></a>
                        <a href="index.php?page=watchlist&action=remove&id=<?= (int)$item['id'] ?>"><button>Entfernen</button></a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Deine Favoritenliste ist leer.</p>
        <?php endif; ?>
    </section>
</div>

<button id="scrollTopBtn" title="Nach oben">⬆</button>
<script src="js/style_modification.js"></script>
<?php include 'view/layout/footer.php'; ?>