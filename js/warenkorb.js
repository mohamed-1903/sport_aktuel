document.addEventListener("DOMContentLoaded", () => {
  loadList();
  updateCartButtons();
  updateCartCount();
});

// 🧠 Globale Button-Steuerung für ALLE .btn-add-to-cart Buttons
document.addEventListener("click", (e) => {
  const btn = e.target.closest(".btn-add-to-cart");
  if (!btn) return;

  let iid = parseInt(btn.dataset.iid);
  if (isNaN(iid)) {
    const parent = btn.closest("[data-iid]");
    iid = parent ? parseInt(parent.dataset.iid) : NaN;
  }

  if (isNaN(iid)) {
    console.warn("❌ Ungültige Produkt-ID.");
    return;
  }

  // 🔍 Prüfen ob bereits im Warenkorb
  const cart = JSON.parse(localStorage.getItem("sammelliste")) || [];
  const alreadyInCart = cart.find((item) => item.id === iid);

  if (alreadyInCart) {
    toggleCart(iid, btn);
    flyToTarget(btn, "#cart-button");
    return;
  }

  // 🧪 Falls nicht vorhanden – jetzt erst Größe und Menge prüfen
  const container = btn.closest('[data-product-index]');
  const idx = container ? container.dataset.productIndex : null;
  const sizeSelect = idx ? container.querySelector(`#size-${idx}`) : null;
  const quantityInput = idx ? container.querySelector(`#quantity-${idx}`) : null;

  if (sizeSelect && quantityInput) {
    const size = sizeSelect.value;
    const quantity = parseInt(quantityInput.value);

    if (!size) {
      alert("❗ Bitte eine Größe auswählen.");
      return;
    }

    if (!quantity || quantity <= 0) {
      alert("❗ Bitte eine gültige Menge angeben.");
      return;
    }

    toggleCart(iid, btn, size, quantity);
    flyToTarget(btn, "#cart-button");
  } else {
    // z. B. auf Kategorie-Seite (kein size/qty wählbar)
    toggleCart(iid, btn);
    flyToTarget(btn, "#cart-button");
  }
});

function toggleCart(iid, btn = null, size = "M", qty = 1) {
  const price = parseFloat(btn?.dataset.price) || 0;
  const key = "sammelliste";
  let cart = JSON.parse(localStorage.getItem(key)) || [];

  const index = cart.findIndex((item) => item.id === iid);
  if (index !== -1) {
    cart.splice(index, 1);
    if (btn) btn.textContent = "🛒";
    zeigeToast("❌ Produkt wurde aus dem Warenkorb entfernt", "#cc0000");
  } else {
    const name = btn?.dataset.name || "Produkt";
    const image = btn?.dataset.image || "img/placeholder.jpg";

    // 🧩 NEU: Rabatt & Geschenk (nur auf Produktseite verfügbar)
    const gift = document.getElementById("giftWrap")?.checked || false;
    const pin = document.getElementById("pin")?.value.trim();
    const discount = DISCOUNT_CODES[pin] || 0;
    cart.push({ id: iid, name, price, image, size, qty, discount, gift });

    if (btn) btn.textContent = "✅";
    zeigeToast("🛒 Produkt wurde zum Warenkorb hinzugefügt", "#28a745");
    zeigeProduktPreview({ name, image, price, size, qty });
  }

  localStorage.setItem(key, JSON.stringify(cart));
  updateCartCount();
}

function updateCartButtons() {
  const cart = JSON.parse(localStorage.getItem("sammelliste")) || [];
  const ids = cart.map((item) => item.id);
  document.querySelectorAll(".btn-add-to-cart").forEach((btn) => {
    let iid = parseInt(btn.dataset.iid);
    if (ids.includes(iid)) {
      btn.textContent = "✅";
    } else {
      btn.textContent = "🛒";
    }
  });
}

function updateCartCount() {
  const cart = JSON.parse(localStorage.getItem("sammelliste")) || [];
  const cartButton = document.getElementById("cart-button");
  if (cartButton) {
    cartButton.innerHTML = `🛒 (${cart.length})`;
  }
}

// ✅ Warenkorbliste darstellen
function loadList() {
  const items = JSON.parse(localStorage.getItem("sammelliste")) || [];
  const tbody = document.getElementById("cart-table-body");
  const zwischensummeEl = document.getElementById("zwischensumme");
  const gesamtsummeEl = document.getElementById("gesamtsumme");
  const nettoEl = document.getElementById("nettosumme");
  const mwstEl = document.getElementById("mwstbetrag");
  const versandkostenEl = document.getElementById("versandkosten");

  if (!tbody || !zwischensummeEl || !gesamtsummeEl) return;

  tbody.innerHTML = "";

  if (items.length === 0) {
    tbody.innerHTML = `<tr><td colspan="5" style="text-align:center; color: gray;">🛒 Dein Warenkorb ist leer.</td></tr>`;
    zwischensummeEl.textContent = "0,00 €";
    gesamtsummeEl.textContent = "0,00 €";
    nettoEl.textContent = "0,00 €";
    mwstEl.textContent = "0,00 €";
    return;
  }

  let total = 0;

  items.forEach((item, index) => {
    const preis = parseFloat(item.price) || 0;
    const menge = parseInt(item.qty) || 1;
    const rabatt = item.discount || 0;
    const geschenk = item.gift ? 2 : 0; // 2 € Geschenkverpackung pro Stück

    const rabattPreis = preis * (1 - rabatt / 100);
    const einzelpreisMitZuschlag = rabattPreis + geschenk;
    const gesamt = einzelpreisMitZuschlag * menge;
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td style="text-align:left;">
        <div style="display:flex; align-items:center; gap:10px;">
          <img src="${
            item.image || "img/placeholder.jpg"
          }" alt="Bild" width="60" />
          <div>
            <strong>${item.name}</strong><br>
            <small>Größe: ${item.size}</small><br>
            ${item.gift ? "<small>🎁 Geschenkverpackung</small><br>" : ""}
            ${item.discount ? `<small>🎟️ Rabatt: ${rabatt}%</small>` : ""}
          </div>
        </div>
      </td>
      <td>
        <select class="menge-select">
          ${[...Array(10)]
            .map(
              (_, i) =>
                `<option value="${i + 1}" ${
                  i + 1 === menge ? "selected" : ""
                }>${i + 1}</option>`
            )
            .join("")}
        </select>
      </td>
      <td>${einzelpreisMitZuschlag.toFixed(2)} €</td>
      <td class="summe-cell">${gesamt.toFixed(2)} €</td>
      <td><button class="remove-btn">❌</button></td>
    `;

    // 🗑 Entfernen-Button
    tr.querySelector(".remove-btn").addEventListener("click", () => {
      items.splice(index, 1);
      localStorage.setItem("sammelliste", JSON.stringify(items));
      loadList();
      updateCartButtons();
      updateCartCount();
    });

    // 🔁 Mengenänderung live
    tr.querySelector(".menge-select").addEventListener("change", (e) => {
      const neueMenge = parseInt(e.target.value);
      items[index].qty = neueMenge;
      localStorage.setItem("sammelliste", JSON.stringify(items));
      loadList();
    });

    tbody.appendChild(tr);
    total += gesamt;
  });

  const netto = total / 1.19;
  const mwst = total - netto;

  nettoEl.textContent = `${netto.toFixed(2)} €`;
  mwstEl.textContent = `${mwst.toFixed(2)} €`;
  gesamtsummeEl.textContent = `${total.toFixed(2)} €`;
  zwischensummeEl.textContent = `${total.toFixed(2)} €`; // falls gewünscht
}
function zeigeToast(text, farbe = "#333") {
  const el = document.getElementById("toast-popup");
  if (!el) return;

  el.textContent = text;
  el.style.backgroundColor = farbe;
  el.classList.add("show");

  clearTimeout(el._hideTimer);
  el._hideTimer = setTimeout(() => {
    el.classList.remove("show");
  }, 2500);
}
function zeigeProduktPreview({ name, image, price, size = "M", qty = 1 }) {
  const popup = document.getElementById("cart-preview-popup");
  if (!popup) return;

  popup.innerHTML = `
    <img src="${image}" alt="${name}">
    <div style="overflow: hidden;">
      <strong>${name}</strong>
      <small>Preis: ${price.toFixed(2)} €</small>
      <small>Größe: ${size} | Menge: ${qty}</small>
    </div>
  `;

  popup.style.display = "block";

  clearTimeout(popup._hideTimer);
  popup._hideTimer = setTimeout(() => {
    popup.style.display = "none";
  }, 4000);
}
function zeigeCartRemovePreview({ name, image }) {
  const popup = document.getElementById("cart-popup");
  if (!popup) return;
  popup.innerHTML = `
    <div class="popup-content removed">
      <img src="${image}" alt="${name}" />
      <div>
        <strong>${name}</strong><br>
        <small>❌ entfernt aus dem Warenkorb</small>
      </div>
    </div>
  `;
  popup.classList.add("show");
  setTimeout(() => popup.classList.remove("show"), 3000);
}

// 🧹 Alles löschen
function clearList() {
  if (confirm("Wirklich den ganzen Warenkorb löschen?")) {
    localStorage.removeItem("sammelliste");
    loadList();
    updateCartButtons();
    updateCartCount();
  }
}

// 💳 Checkout
function checkout() {
  fetch("check_login.php")
    .then((res) => res.json())
    .then((data) => {
      if (data.loggedIn) {
        alert("✅ Danke für deinen Einkauf! (Checkout in Entwicklung)");
        // Hier könntest du z. B. save_order.php aufrufen
      } else {
        // 🔁 Weiterleitung zur Loginseite mit Rücksprung
        window.location.href = "login.php?redirect=warenkorb.php";
      }
    });
}

