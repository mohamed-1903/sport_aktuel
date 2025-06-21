<?php include 'view/layout/header.php'; ?>

<div class="produkte">
    <h1>🧡 Deine Merkliste</h1>
    <div id="watchlist-container" class="einzelprodukt-grid"></div>

    <div style="text-align: center; margin-top: 20px;">
        <button class="btn-delete-all" onclick="clearWatchlist()">🧹 Alle löschen</button>
    </div>

</div>

<!-- Produktdaten direkt ins JS einbinden -->
<script id="json-data" type="application/json">
    <?= file_get_contents("data/products.json"); ?>
</script>
<button id="scrollTopBtn" title="Nach oben">⬆</button>

<script src="js/style_modification.js"></script>
<script src="js/watchlist.js"></script>
<script src="js/filterandsearch.js"></script>
<script src="js/produkt.js"></script>