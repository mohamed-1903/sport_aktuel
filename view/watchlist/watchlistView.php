<?php include 'view/layout/header.php'; ?>

<div class="produkte">
    <h1>🧡 Deine Merkliste</h1>
    <div id="watchlist-container" class="einzelprodukt-grid">
        <?php if (empty($watchlistItems)): ?>
            <p style="color: gray;">🤍 Noch keine Produkte auf deiner Merkliste.</p>
        <?php else: ?>
            <?php foreach ($watchlistItems as $item): ?>
                <div class="watchlist-card" data-id="<?= (int)$item['product_id'] ?>">
                    <div class="image-wrapper">
                        <img src="<?= htmlspecialchars($item['image_main']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                    </div>
                    <div class="produkt-info">
                        <h3><?= htmlspecialchars($item['name']) ?></h3>
                        <p><?= number_format($item['price'], 2, ',', '.') ?> €</p>
                        <form action="index.php?page=watchlist&action=remove" method="post" style="display:inline;">
                            <input type="hidden" name="id" value="<?= (int)$item['product_id'] ?>">
                            <button type="submit">🗑️ Entfernen</button>
                        </form>
                        <a href="index.php?page=product&action=detail&id=<?= (int)$item['product_id'] ?>"><button>🔍 Anzeigen</button></a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div style="text-align: center; margin-top: 20px;">
        <a href="index.php?page=watchlist&action=clear" class="btn-delete-all">🧹 Alle löschen</a>
    </div>
</div>

<button id="scrollTopBtn" title="Nach oben">⬆</button>
<?php include 'view/layout/footer.php'; ?>

