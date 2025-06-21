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

  const sizeSelect = btn
    .closest(".Eprodukt")
    ?.querySelector("select.size-dropdown");
  const quantityInput = btn
    .closest(".Eprodukt")
    ?.querySelector("input[type=number]");
  const size = sizeSelect?.value || "M";
  const quantity = parseInt(quantityInput?.value) || 1;

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
});

function toggleCart(iid, btn = null, size = "M", qty = 1) {
  const payload = { product_id: iid, size, qty };

  fetch("index.php?page=cart&action=add", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success || data.in_cart) {
        zeigeToast("🛒 Zum Warenkorb hinzugefügt", "#28a745");
        if (btn) btn.textContent = "✅";
        updateCartCount(); // neu hinzufügen
        loadList(); // Liste neu laden
      } else {
        zeigeToast("⚠️ Fehler: " + (data.error || "Unbekannt"), "#cc0000");
      }
    })
    .catch((err) => {
      zeigeToast("⚠️ Serverfehler beim Hinzufügen", "#cc0000");
      console.error(err);
    });
}

function updateCartButtons() {
  fetch("index.php?page=cart&action=json")
    .then((res) => res.json())
    .then((items) => {
      document.querySelectorAll(".btn-add-to-cart").forEach((btn) => {
        const iid = parseInt(btn.dataset.iid);
        const parent = btn.closest(".Eprodukt") || btn.closest("[data-iid]");
        const sizeSelect = parent?.querySelector("select.size-dropdown");
        const size = sizeSelect?.value || "M";

        const isInCart = items.some(
          (item) => item.product_id == iid && item.size === size
        );

        btn.textContent = isInCart ? "✅" : "🛒";
      });
    })
    .catch((err) =>
      console.error("Fehler beim Aktualisieren der Cart-Buttons:", err)
    );
}

function updateCartCount() {
  fetch("index.php?page=cart&action=count")
    .then((res) => res.json())
    .then((data) => {
      const cartButton = document.getElementById("cart-button");
      if (cartButton) {
        cartButton.innerHTML = `🛒 (${data.count || 0})`;
      }
    });
}

// ✅ Warenkorbliste darstellen
function loadList() {
  fetch("index.php?page=cart&action=json")
    .then((res) => res.json())
    .then((items) => {
      const tbody = document.getElementById("cart-table-body");
      const zwischensummeEl = document.getElementById("zwischensumme");
      const gesamtsummeEl = document.getElementById("gesamtsumme");
      const nettoEl = document.getElementById("nettosumme");
      const mwstEl = document.getElementById("mwstbetrag");

      if (!tbody || !items) return;

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

      items.forEach((item) => {
        const preis = parseFloat(item.price) || 0;
        const menge = parseInt(item.quantity) || 1;
        const gesamt = preis * menge;

        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td style="text-align:left;">
            <div style="display:flex; align-items:center; gap:10px;">
              <img src="${item.image_main}" alt="Bild" width="60" />
              <div>
                <strong>${item.name}</strong><br>
                <small>Größe: ${item.size}</small>
              </div>
            </div>
          </td>
          <td>${menge}</td>
          <td>${preis.toFixed(2)} €</td>
          <td class="summe-cell">${gesamt.toFixed(2)} €</td>
          <td>
            <button class="remove-btn" data-id="${
              item.product_id
            }" data-size="${item.size}">❌</button>
          </td>
        `;

        tbody.appendChild(tr);
        total += gesamt;
      });

      const netto = total / 1.19;
      const mwst = total - netto;
      zwischensummeEl.textContent = `${total.toFixed(2)} €`;
      gesamtsummeEl.textContent = `${total.toFixed(2)} €`;
      nettoEl.textContent = `${netto.toFixed(2)} €`;
      mwstEl.textContent = `${mwst.toFixed(2)} €`;

      // 🗑 EventListener zum Entfernen
      document.querySelectorAll(".remove-btn").forEach((btn) => {
        btn.addEventListener("click", () => {
          const id = btn.dataset.id;
          const size = btn.dataset.size;
          removeFromCart(id, size);
        });
      });
    });
}
function removeFromCart(productId, size) {
  fetch("index.php?page=cart&action=remove", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `id=${encodeURIComponent(productId)}&size=${encodeURIComponent(
      size
    )}`,
  })
    .then(() => loadList())
    .catch((err) => console.error("Fehler beim Entfernen:", err));
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
    fetch("index.php?page=cart&action=clear").then(() => {
      loadList();
      updateCartButtons();
      updateCartCount();
    });
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
