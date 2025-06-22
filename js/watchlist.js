document.addEventListener("DOMContentLoaded", () => {
  loadWatchlist();
  updateWatchButtons();
  updateWatchlistCount();
});
// avoid conflicts with other scripts
const isOnProductDetailPageWatch =
  window.location.href.includes("page=product") &&
  window.location.href.includes("action=detail");

document.addEventListener("click", (e) => {
  const btn = e.target.closest(".btn-add-to-watch");
  if (!btn) return;

  let iid = parseInt(btn.dataset.iid);
  if (isNaN(iid)) {
    const parent = btn.closest("[data-iid]");
    iid = parent ? parseInt(parent.dataset.iid) : NaN;
  }
  if (isNaN(iid)) return;

  toggleWatchlist(iid, btn);
});

function toggleWatchlist(iid, btn = null) {
  fetch("index.php?page=watchlist&action=toggle", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ product_id: iid }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.status === "ok") {
        if (btn) btn.textContent = data.in_watchlist ? "❤️" : "🤍";
        loadWatchlist();

        const name = btn?.dataset.name || "Produkt";
        const image = btn?.dataset.image || "img/placeholder.jpg";
        const price = parseFloat(btn?.dataset.price) || 0;

        if (data.in_watchlist) {
          if (btn) btn.textContent = "❤️";
          flyToTarget(btn, "#watchlist-button", "❤️");
          zeigeWatchPreview({ name, image, price });
          zeigeWatchButtonBestaetigung(); // zeigt oben im Button nur ❤️
          setTimeout(() => {
            updateWatchlistCount(); // aktualisiert danach den Zähler
          }, 2000);
          zeigeToast("❤️ Produkt wurde zur Merkliste hinzugefügt", "#28a745");
        } else {
          if (btn) btn.textContent = "🤍";
          flyToTarget(btn, "#watchlist-button", "🤍");
          zeigeWatchRemovePreview({ name, image, productId: iid });
          updateWatchlistCount(); // sofort aktualisieren bei Entfernen
          zeigeToast("💔 Produkt wurde aus der Merkliste entfernt", "#cc0000");
        }
      } else {
        zeigeToast("Fehler bei der Merkliste", "#cc0000");
      }
    })
    .catch((err) => console.error("Watchlist Toggle Error", err));
}

function toggleWatchlistBulk(ids = []) {
  if (!Array.isArray(ids) || ids.length === 0) return;
  fetch("index.php?page=watchlist&action=toggleBulk", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ product_ids: ids }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.status === "ok") {
        updateWatchlistCount();
        updateWatchButtons();
        loadWatchlist();
        zeigeToast("🔁 Merkliste aktualisiert", "#28a745");
      } else {
        zeigeToast("Fehler bei der Merkliste", "#cc0000");
      }
    })
    .catch((err) => console.error("Watchlist Bulk Error", err));
}

function updateWatchButtons() {
  fetch("index.php?page=watchlist&action=json")
    .then((res) => res.json())
    .then((items) => {
      document.querySelectorAll(".btn-add-to-watch").forEach((btn) => {
        let iid = parseInt(btn.dataset.iid);
        if (isNaN(iid)) {
          const parent = btn.closest("[data-iid]");
          iid = parent ? parseInt(parent.dataset.iid) : NaN;
        }
        if (!isNaN(iid)) {
          const isIn = items.some((it) => it.product_id == iid);
          btn.textContent = isIn ? "❤️" : "🤍";
        }
      });
    });
}

function updateWatchlistCount() {
  fetch("index.php?page=watchlist&action=count")
    .then((res) => res.json())
    .then((data) => {
      const el = document.getElementById("watchlist-button");
      if (el) el.innerHTML = `&#10084; (${data.count || 0})`;
    });
}

function loadWatchlist() {
  const container = document.getElementById("watchlist-container");
  if (!container) return;

  fetch("index.php?page=watchlist&action=json")
    .then((res) => res.json())
    .then((items) => {
      container.innerHTML = "";
      if (items.length === 0) {
        container.innerHTML =
          "<p style='color: gray;'>🤍 Noch keine Produkte auf deiner Merkliste.</p>";
        return;
      }
      items.forEach((item) => {
        const card = document.createElement("div");
        card.className = "watchlist-card";
        card.setAttribute("data-iid", item.product_id);
        card.innerHTML = `
          <div class="image-wrapper"><img src="${item.image_main}" alt="${
          item.name
        }"></div>
          <div class="produkt-info">
            <h3>${item.name}</h3>
            <p>${parseFloat(item.price).toFixed(2)} €</p>
            <button class="remove-watch" data-id="${
              item.product_id
            }" data-name="${item.name}" data-image="${item.image_main}">🗑️ Entfernen</button>
            <a href="index.php?page=product&action=detail&id=${
              item.product_id
            }"><button>🔍 Anzeigen</button></a>
          </div>`;
        container.appendChild(card);
      });

      container.querySelectorAll(".remove-watch").forEach((btn) => {
        btn.addEventListener("click", () => {
          removeFromWatchlist(btn.dataset.id, {
            name: btn.dataset.name,
            image: btn.dataset.image,
          });
        });
      });
    });
}

function removeFromWatchlist(id, info = {}) {
  fetch("index.php?page=watchlist&action=remove", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `id=${encodeURIComponent(id)}`,
  }).then(() => {
    loadWatchlist();
    updateWatchButtons();
    updateWatchlistCount();
    if (info.name && info.image) {
      zeigeWatchRemovePreview({
        name: info.name,
        image: info.image,
        productId: id,
      });
    }
  });
}

function clearWatchlist() {
  if (
    confirm("Willst du wirklich alle Produkte aus der Merkliste entfernen?")
  ) {
    fetch("index.php?page=watchlist&action=clear").then(() => {
      loadWatchlist();
      updateWatchButtons();
      updateWatchlistCount();
    });
  }
}
function zeigeWatchButtonBestaetigung() {
  const watchBtn = document.getElementById("watchlist-button");
  if (!watchBtn) return;

  const original = watchBtn.dataset.originalText || watchBtn.innerHTML;
  watchBtn.dataset.originalText = original;

  watchBtn.innerHTML = "❤️";

  clearTimeout(watchBtn._resetTimer);
  watchBtn._resetTimer = setTimeout(() => {
    watchBtn.innerHTML = watchBtn.dataset.originalText;
  }, 2000);
}

// 🔔 Popup bei Hinzufügen zur Watchlist
function zeigeWatchPreview({ name, image, price, productId }) {
  const popup = document.getElementById("watchlist-preview-popup");
  if (!popup) return;

  // Prüfen, ob wir uns auf der Produktdetailseite befinden
  const isOnProductDetailPageWatch =
    window.location.href.includes("page=product") &&
    window.location.href.includes("action=detail");

  popup.innerHTML = `
    <div class="popup-content-flex">
      <img src="${image}" alt="${name}" />
      <div class="popup-text-info">
        <strong>${name}</strong>
        <small>❤️ Zur Merkliste hinzugefügt</small>
        <small>${price.toFixed(2)} €</small>
        <div class="popup-buttons">
          <a href="index.php?page=watchlist&action=view">Merkliste</a>
          ${
            !isOnProductDetailPageWatch
              ? `<a href="index.php?page=product&action=detail&id=${productId}">🔍 Anzeigen</a>`
              : ""
          }
        </div>
      </div>
    </div>
  `;

  popup.classList.add("show");
  clearTimeout(popup._hideTimer);
  popup._hideTimer = setTimeout(() => {
    popup.classList.remove("show");
  }, 4000);
}

// 🔔 Popup bei Entfernen aus der Watchlist
function zeigeWatchRemovePreview({ name, image, productId }) {
  const popup = document.getElementById("watch-popup");
  if (!popup) return;

  const isDetailPage =
    location.href.includes("page=product") &&
    location.href.includes("action=detail");

  popup.innerHTML = `
    <div class="popup-content removed">
      <img src="${image}" alt="${name}" />
      <div class="popup-text">
        <strong>${name}</strong><br>
        <small>💔 wurde aus deiner Merkliste entfernt</small>
        <div class="popup-buttons">
          <button class="undo-btn">↩️ Rückgängig</button>
          ${
            !isDetailPage
              ? `<a href="index.php?page=product&action=detail&id=${productId}" class="show-btn">🔍 Anzeigen</a>`
              : ""
          }
        </div>
      </div>
    </div>
  `;
  popup.classList.add("show");

  // Event: Rückgängig
  popup.querySelector(".undo-btn").addEventListener("click", () => {
    toggleWatchlist(productId); // Wieder hinzufügen
    popup.classList.remove("show");
  });

  setTimeout(() => {
    popup.classList.remove("show");
  }, 5000);
}
