document.addEventListener('DOMContentLoaded', () => {
  loadWatchlist();
  updateWatchButtons();
  updateWatchlistCount();
});

document.addEventListener('click', (e) => {
  const btn = e.target.closest('.btn-add-to-watch');
  if (!btn) return;
  const iid = parseInt(btn.dataset.iid);
  if (isNaN(iid)) return;
  toggleWatchlist(iid, btn);
  flyToTarget(btn, '#watchlist-button');
});

function toggleWatchlist(iid, btn = null) {
  fetch('index.php?page=watchlist&action=toggle', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ product_id: iid })
  })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'ok') {
        if (btn) btn.textContent = data.in_watchlist ? '❤️' : '🤍';
        updateWatchlistCount();
        loadWatchlist();
      } else {
        zeigeToast('Fehler bei der Merkliste', '#cc0000');
      }
    })
    .catch(err => console.error('Watchlist Toggle Error', err));
}

function toggleWatchlistBulk(ids = []) {
  if (!Array.isArray(ids) || ids.length === 0) return;
  fetch('index.php?page=watchlist&action=toggleBulk', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ product_ids: ids })
  })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'ok') {
        updateWatchlistCount();
        updateWatchButtons();
        loadWatchlist();
      } else {
        zeigeToast('Fehler bei der Merkliste', '#cc0000');
      }
    })
    .catch(err => console.error('Watchlist Bulk Error', err));
}

function updateWatchButtons() {
  fetch('index.php?page=watchlist&action=json')
    .then(res => res.json())
    .then(items => {
      document.querySelectorAll('.btn-add-to-watch').forEach(btn => {
        const iid = parseInt(btn.dataset.iid);
        if (isNaN(iid)) return;
        const isIn = items.some(it => it.product_id == iid);
        btn.textContent = isIn ? '❤️' : '🤍';
      });
    });
}

function updateWatchlistCount() {
  fetch('index.php?page=watchlist&action=count')
    .then(res => res.json())
    .then(data => {
      const el = document.getElementById('watchlist-button');
      if (el) el.innerHTML = `&#10084; (${data.count || 0})`;
    });
}

function loadWatchlist() {
  const container = document.getElementById('watchlist-container');
  if (!container) return;

  fetch('index.php?page=watchlist&action=json')
    .then(res => res.json())
    .then(items => {
      container.innerHTML = '';
      if (items.length === 0) {
        container.innerHTML = "<p style='color: gray;'>🤍 Noch keine Produkte auf deiner Merkliste.</p>";
        return;
      }
      items.forEach(item => {
        const card = document.createElement('div');
        card.className = 'watchlist-card';
        card.innerHTML = `
          <div class="image-wrapper"><img src="${item.image_main}" alt="${item.name}"></div>
          <div class="produkt-info">
            <h3>${item.name}</h3>
            <p>${parseFloat(item.price).toFixed(2)} €</p>
            <button class="remove-watch" data-id="${item.product_id}">🗑️ Entfernen</button>
            <a href="index.php?page=product&action=detail&id=${item.product_id}"><button>🔍 Anzeigen</button></a>
          </div>`;
        container.appendChild(card);
      });
      container.querySelectorAll('.remove-watch').forEach(btn => {
        btn.addEventListener('click', () => {
          removeFromWatchlist(btn.dataset.id);
        });
      });
    });
}

function removeFromWatchlist(id) {
  fetch('index.php?page=watchlist&action=remove', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `id=${encodeURIComponent(id)}`
  }).then(() => {
    loadWatchlist();
    updateWatchButtons();
    updateWatchlistCount();
  });
}

function clearWatchlist() {
  if (confirm('Willst du wirklich alle Produkte aus der Merkliste entfernen?')) {
    fetch('index.php?page=watchlist&action=clear').then(() => {
      loadWatchlist();
      updateWatchButtons();
      updateWatchlistCount();
    });
  }