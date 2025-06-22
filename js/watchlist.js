document.addEventListener("DOMContentLoaded", () => {
  const container = document.getElementById("watchlist-container");
  const jsonData = document.getElementById("json-data").textContent;
  const allProducts = JSON.parse(jsonData).products;

  const watchlist = JSON.parse(localStorage.getItem("watchlist")) || [];

  if (watchlist.length === 0) {
    container.innerHTML =
      "<p style='color: gray;'>🤍 Noch keine Produkte auf deiner Merkliste.</p>";
    return;
  }

  const gemerkteProdukte = allProducts.filter((p) => watchlist.includes(p.iid));

  gemerkteProdukte.forEach((p) => {
    const card = document.createElement("div");
    card.classList.add("watchlist-card");
    card.setAttribute("data-iid", p.iid); // 🔧 HIER hinzufügen
    card.innerHTML = `
    <div class="image-wrapper"><img src="${p.imageMain}" alt="${p.name}"></div>
    <div class="produkt-info">
      <h3>${p.name}</h3>
      <p>${p.price}</p>
      <button onclick="removeFromWatchlist(${p.iid})">🗑️ Entfernen</button>
      <a href="Produkt_Sport.php?iid=${p.iid}"><button>🔍 Anzeigen</button></a>
      <button onclick="addToCartFromWatchlist(${p.iid})">🛒 In den Warenkorb</button>
    </div>
  `;
    container.appendChild(card);
  });
});

function removeFromWatchlist(iid) {
  const list = JSON.parse(localStorage.getItem("watchlist")) || [];
  const updated = list.filter((id) => id !== iid);
  localStorage.setItem("watchlist", JSON.stringify(updated));

  // ✅ Zähler aktualisieren + Buttons synchronisieren
  updateWatchlistCount();
  updateAllWatchButtons();
  syncWatchlistWithSession();

  // ✅ Karte entfernen (sanft)
  const card = document.querySelector(`.watchlist-card[data-iid="${iid}"]`);
  if (card) {
    card.classList.add("fade-out");
    setTimeout(() => {
      card.remove();

      // ❗ Danach prüfen, ob noch Produkte übrig sind
      const remaining = document.querySelectorAll(".watchlist-card");
      if (remaining.length === 0) {
        const container = document.getElementById("watchlist-container");
        container.innerHTML =
          "<p style='color: gray;'>🤍 Noch keine Produkte auf deiner Merkliste.</p>";
      }
    }, 300);
  }
}

function toggleWatchlist(iid, el = null) {
  const key = "watchlist";
  let list = JSON.parse(localStorage.getItem(key)) || [];

  const index = list.indexOf(iid);
  if (index !== -1) {
    list.splice(index, 1);
    if (el) el.textContent = "🤍";
    zeigeToast("💔 Produkt wurde aus der Merkliste entfernt", "#cc0000");
    const name = el?.dataset.name || "Produkt";
    const image = el?.dataset.image || "img/placeholder.jpg";
    zeigeWatchRemovePreview({ name, image });
  } else {
    list.push(iid);
    if (el) el.textContent = "❤️";
    zeigeToast("❤️ Produkt wurde zur Merkliste hinzugefügt", "#28a745");
    zeigeWatchPreview({
      name: el?.dataset.name || "Produkt",
      image: el?.dataset.image || "img/placeholder.jpg",
      price: parseFloat(el?.dataset.price) || 0,
    });
  }

  localStorage.setItem(key, JSON.stringify(list));
  updateWatchlistCount();
  syncWatchlistWithSession(); // ← HIER AUFRUFEN
}
document.addEventListener("DOMContentLoaded", () => {
  updateWatchlistCount();
  updateAllWatchButtons(); // Buttons visuell setzen
});

// 🧠 Event Delegation für ALLE Buttons, auch dynamisch erzeugte
document.addEventListener("click", (e) => {
  const btn = e.target.closest(".btn-add-to-watch");
  if (!btn) return;

  // 🔍 ID dynamisch ermitteln: entweder vom Button oder Eltern
  let iid = parseInt(btn.dataset.iid);
  if (isNaN(iid)) {
    const parent = btn.closest("[data-iid]");
    iid = parent ? parseInt(parent.dataset.iid) : NaN;
  }

  if (isNaN(iid)) {
    console.warn("⚠️ Keine gültige Produkt-ID gefunden.");
    return;
  }

  toggleWatchlist(iid, btn);
  flyToTarget(btn, "#watchlist-button");
});
function updateAllWatchButtons() {
  const list = JSON.parse(localStorage.getItem("watchlist")) || [];
  document.querySelectorAll(".btn-add-to-watch").forEach((btn) => {
    let iid = parseInt(btn.dataset.iid);
    if (isNaN(iid)) {
      const parent = btn.closest("[data-iid]");
      iid = parent ? parseInt(parent.dataset.iid) : NaN;
    }
    if (!isNaN(iid)) {
      btn.textContent = list.includes(iid) ? "❤️" : "🤍";
    }
  });
}
function clearWatchlist() {
  if (
    confirm("Willst du wirklich alle Produkte aus der Merkliste entfernen?")
  ) {
    localStorage.removeItem("watchlist");
    location.reload();
    syncWatchlistWithSession(); // ← HIER AUFRUFEN
  }
}
function addToCartFromWatchlist(iid) {
  const jsonData = document.getElementById("json-data").textContent;
  const allProducts = JSON.parse(jsonData).products;
  const product = allProducts.find((p) => p.iid === iid);

  if (!product) {
    zeigeToast("❌ Produkt nicht gefunden", "#cc0000");
    return;
  }

  // Basis-Warenkorb-Eintrag
  const item = {
    id: product.iid,
    name: product.name,
    qty: 1,
    size: "M", // Default-Größe (später auswählbar machen)
    price: product.priceValue.toFixed(2),
  };

  let cart = JSON.parse(localStorage.getItem("sammelliste")) || [];

  // Optional: Duplikate vermeiden
  const exists = cart.find((i) => i.id === item.id);
  if (exists) {
    zeigeToast("✅ Produkt ist bereits im Warenkorb", "#007bff");
    zeigeProduktPreview({
      name: product.name,
      image: product.imageMain,
      price: parseFloat(product.priceValue),
      size: "M",
      qty: 1,
    });
    return;
  }

  cart.push(item);
  localStorage.setItem("sammelliste", JSON.stringify(cart));
  zeigeToast("🛒 Produkt wurde zum Warenkorb hinzugefügt", "#28a745");
}
function updateWatchlistCount() {
  const count = JSON.parse(localStorage.getItem("watchlist"))?.length || 0;
  const watchlistButton = document.getElementById("watchlist-button");
  if (watchlistButton) {
    watchlistButton.innerHTML = `&#10084; (${count})`;
  }
}
function syncWatchlistWithSession() {
  const list = JSON.parse(localStorage.getItem("watchlist")) || [];

  fetch("index.php?page=watchlist&action=sync", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ watchlist: list }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.status === "success") {
        console.log("✅ Merkliste wurde auf dem Server gespeichert.");
      } else {
        console.warn("⚠️ Fehler beim Speichern:", data.message);
      }
    });
}
document.addEventListener("click", (e) => {
  const addToCartBtn = e.target.closest(".btn-add-to-cart");

  if (addToCartBtn) {
    const iid = parseInt(addToCartBtn.dataset.iid);
    if (isNaN(iid)) return;

    // 🧠 Optional: Daten aus Merkliste holen
    const wishlist = JSON.parse(localStorage.getItem("merkliste")) || [];
    const product = wishlist.find((item) => item.id === iid);
    if (!product) return;

    // 🛒 In den Warenkorb übertragen
    const cart = JSON.parse(localStorage.getItem("sammelliste")) || [];
    cart.push(product);
    localStorage.setItem("sammelliste", JSON.stringify(cart));

    // ✅ Fly-Animation starten
    flyToTarget(addToCartBtn, "#cart-button");

    // 🔄 UI aktualisieren
    updateCartButtons?.();
    updateCartCount?.();
  }
});
function zeigeWatchPreview({ name, image, price }) {
  const popup = document.getElementById("watchlist-preview-popup");
  if (!popup) return;

  popup.innerHTML = `
    <img src="${image}" alt="${name}">
    <div style="overflow: hidden;">
      <strong>${name}</strong>
      <small>Preis: ${price.toFixed(2)} €</small>
      <small>❤️ Zur Merkliste hinzugefügt</small>
    </div>
  `;

  popup.style.display = "block";

  clearTimeout(popup._hideTimer);
  popup._hideTimer = setTimeout(() => {
    popup.style.display = "none";
  }, 4000);
}
function zeigeWatchRemovePreview({ name, image }) {
  const popup = document.getElementById("watch-popup");
  if (!popup) return;
  popup.innerHTML = `
    <div class="popup-content removed">
      <img src="${image}" alt="${name}" />
      <div>
        <strong>${name}</strong><br>
        <small>💔 entfernt aus der Merkliste</small>
      </div>
    </div>
  `;
  popup.classList.add("show");
  setTimeout(() => popup.classList.remove("show"), 4000);
}
