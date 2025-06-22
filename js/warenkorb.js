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

  const sizeSelect = document.getElementById("size");
  const quantityInput = document.getElementById("quantity");

  let size = "M";
  let quantity = 1;
  if (sizeSelect && quantityInput) {
    size = sizeSelect.value;
    quantity = parseInt(quantityInput.value);

    if (!size) {
      alert("❗ Bitte eine Größe auswählen.");
      return;
    }
    if (!quantity || quantity <= 0) {
      alert("❗ Bitte eine gültige Menge angeben.");
      return;
    }
  }

  toggleCart(iid, btn, size, quantity);
  flyToTarget(btn, "#cart-button");
});

function toggleCart(iid, btn = null, size = "M", qty = 1) {
  const price = parseFloat(btn?.dataset.price) || 0;
  const name = btn?.dataset.name || "Produkt";
  const image = btn?.dataset.image || "img/placeholder.jpg";


  const gift = document.getElementById("giftWrap")?.checked || false;
  const pin = document.getElementById("pin")?.value.trim();
  const discount = DISCOUNT_CODES[pin] || 0;

  const payload = { id: iid, size, quantity: qty };

  fetch("index.php?page=cart&action=toggle", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload),
  })
    .then((res) => {
      if (!res.ok) throw new Error("HTTP " + res.status);
      return res.json();
    })
    .then((data) => {
      if (data.status === "ok") {
        if (data.in_cart) {
          if (btn) btn.textContent = "✅";
          zeigeToast("🛒 Produkt wurde zum Warenkorb hinzugefügt", "#28a745");
          zeigeProduktPreview({ name, image, price, size, qty });
        } else {
          if (btn) btn.textContent = "🛒";
          zeigeToast("❌ Produkt wurde aus dem Warenkorb entfernt", "#cc0000");
          zeigeCartRemovePreview({ name, image });
        }
        updateCartButtons();
        updateCartCount();
        loadList();
      } else {
        zeigeToast("⚠️ Fehler: " + (data.message || "Unbekannt"), "#cc0000");
      }
    })
    .catch((err) => {
      console.error(err);
      zeigeToast("⚠️ Serverfehler beim Hinzufügen", "#cc0000");
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

        const inCart = items.some(
          (item) => item.product_id == iid && item.size === size
        );
        btn.textContent = inCart ? "✅" : "🛒";
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
        const rabatt = item.discount || 0;
        const geschenk = item.gift ? 2 : 0;

        const rabattPreis = preis * (1 - rabatt / 100);
        const einzelpreisMitZuschlag = rabattPreis + geschenk;
        const gesamt = einzelpreisMitZuschlag * menge;

        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td style="text-align:left;">
            <div style="display:flex; align-items:center; gap:10px;">
              <img src="${item.image_main}" alt="Bild" width="60" />
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
          <td><button class="remove-btn" data-id="${item.product_id}" data-size="${item.size}">❌</button></td>
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

      document.querySelectorAll(".remove-btn").forEach((btn) => {
        btn.addEventListener("click", () => {
          removeFromCart(btn.dataset.id, btn.dataset.size);
        });
      });

      document.querySelectorAll(".menge-select").forEach((select) => {
        select.addEventListener("change", (e) => {
          const tr = e.target.closest("tr");
          const id = tr.querySelector(".remove-btn").dataset.id;
          const size = tr.querySelector(".remove-btn").dataset.size;
          const quantity = parseInt(e.target.value);
          updateQuantity(id, size, quantity);
        });
      });
    });
}

function removeFromCart(productId, size) {
  fetch("index.php?page=cart&action=remove", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `id=${encodeURIComponent(productId)}&size=${encodeURIComponent(size)}`,
  })
    .then(() => {
      loadList();
      updateCartButtons();
      updateCartCount();
    })
    .catch((err) => console.error("Fehler beim Entfernen:", err));
}

function updateQuantity(productId, size, quantity) {
  fetch("index.php?page=cart&action=update", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `id=${encodeURIComponent(productId)}&size=${encodeURIComponent(size)}&quantity=${encodeURIComponent(quantity)}`,
  }).then(() => {
    loadList();
    updateCartCount();
  });
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

function clearList() {
  if (confirm("Wirklich den ganzen Warenkorb löschen?")) {
    fetch("index.php?page=cart&action=clear").then(() => {
      loadList();
      updateCartButtons();
      updateCartCount();
    });
  }
}

function checkout() {
  fetch("check_login.php")
    .then((res) => res.json())
    .then((data) => {
      if (data.loggedIn) {
        alert("✅ Danke für deinen Einkauf! (Checkout in Entwicklung)");
      } else {
        window.location.href = "login.php?redirect=warenkorb.php";
      }
    });
}