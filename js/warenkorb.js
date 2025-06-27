document.addEventListener("DOMContentLoaded", () => {
  loadList();
  updateCartButtons();
  updateCartCount();
});
// avoid conflicts with watchlist script
const isOnProductDetailPageCart =
  window.location.href.includes("page=product") &&
  window.location.href.includes("action=detail");

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
  const size = sizeSelect ? sizeSelect.value : "M";
  const quantity = parseInt(quantityInput?.value) || 1;

  if (sizeSelect && !size) {
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
  const section = btn?.closest(".Eprodukt");
  let discount = 0;
  let gift = false;

  if (section) {
    const idx = section.dataset.productIndex;
    const pin = section.querySelector(`#pin-${idx}`)?.value.trim();
    discount = window.DISCOUNT_CODES?.[pin] || 0;
    gift = section.querySelector(`#giftWrap-${idx}`)?.checked || false;
  }

  const payload = { id: iid, size, quantity: qty, discount, gift };
  if (section) {
    const nameInput = section.querySelector(".custom-name");
    const numberInput = section.querySelector(".custom-number");
    const hasCustom =
      (nameInput && nameInput.value.trim()) ||
      (numberInput && numberInput.value.trim());
    if (nameInput) payload.custom_name = nameInput.value.trim();
    if (numberInput) payload.custom_number = numberInput.value.trim();
    if (hasCustom) payload.custom_fee = window.CUSTOMIZATION_FEE || 0;
  }

  fetch("index.php?page=cart&action=add", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.status === "ok" || data.in_cart) {
        zeigeToast("🛒 Zum Warenkorb hinzugefügt", "#28a745");
        if (btn) zeigeButtonBestaetigung(btn);
        if (btn) {
          const name = btn.dataset.name;
          const image = btn.dataset.image;
          const price = parseFloat(btn.dataset.price) || 0;
          // Gestapeltes Popup anzeigen
          const buttons = `
            <button class="remove-cart-btn" data-id="${iid}" data-size="${size}">Entfernen</button>
            <a href="index.php?page=cart&action=view">Warenkorb</a>
            ${
              !isOnProductDetailPageCart
                ? `<a href="index.php?page=product&action=detail&id=${iid}" class="show-btn">Anzeigen</a>`
                : ""
            }`;

          zeigeGestapeltesPopup({
            name,
            image,
            message: `In den Warenkorb gelegt (${size}, ${qty}x)`,
            productId: iid,
            icon: "🛒",
            buttons,
            onInit: (popup) => {
              const rm = popup.querySelector(".remove-cart-btn");
              if (rm) {
                rm.addEventListener("click", () => {
                  removeFromCart(iid, size, { name, image, productId: iid });
                  popup.classList.add("fade-out");

                  setTimeout(() => popup.remove(), 400);
                });
              }
            },
          });
        }
        updateCartCount((cnt) => zeigeCartBestaetigung(cnt));
        loadList();
      } else {
        zeigeToast("⚠️ Fehler: " + (data.error || "Unbekannt"), "#cc0000");
      }
    })
    .catch((err) => {
      zeigeToast("⚠️ Serverfehler beim Hinzufügen", "#cc0000");
      console.error(err);
    });
}

function updateCartCount(callback) {
  fetch("index.php?page=cart&action=count")
    .then((res) => res.json())
    .then((data) => {
      const el = document.getElementById("cart-button");
      if (el) el.innerHTML = `&#128722; (${data.count || 0})`;

      if (typeof callback === "function") {
        callback(data.count || 0);
      }
    })
    .catch((err) => console.error("Warenkorb-Zähler Fehler:", err));
}

function zeigeCartBestaetigung(count) {
  const el = document.getElementById("cart-button");
  if (!el) return;

  const original = `&#128722; (${count})`;
  el.innerHTML = "✅";

  clearTimeout(el._resetTimer);
  el._resetTimer = setTimeout(() => {
    el.innerHTML = original;
  }, 2000);
}

function zeigeButtonBestaetigung(btn) {
  if (!btn) return;
  const original = btn.textContent;
  btn.textContent = "✅";
  clearTimeout(btn._resetTimer);
  btn._resetTimer = setTimeout(() => {
    btn.textContent = original;
  }, 2000);
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
        const discount = parseInt(item.discount) || 0;
        const gift = item.gift == 1;
        const custom = parseFloat(item.custom_fee) || 0;
        const einzelfpreis =
          preis * (1 - discount / 100) + (gift ? 2 : 0) + custom;
        const gesamt = einzelfpreis * menge;

        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td style="text-align:left;">
            <div style="display:flex; align-items:center; gap:10px;">
              <img src="${item.image_main}" alt="Bild" width="60" />
              <div>
                <strong>${item.name}</strong><br>
                <small>Größe: ${item.size}</small><br>
                ${
                  item.custom_name || item.custom_number
                    ? `<small>Personalisierung: ${item.custom_name || ""} ${
                        item.custom_number || ""
                      }</small><br>`
                    : ""
                }
                ${
                  item.gift == 1
                    ? "<small>🎁 Geschenkverpackung</small><br>"
                    : ""
                }
                ${
                  item.discount
                    ? `<small>🎟️ Rabatt: ${item.discount}%</small>`
                    : ""
                }
              </div>
            </div>
          </td>
          <td>
            <select class="qty-select" data-id="${
              item.product_id
            }" data-size="${item.size}" data-price="${einzelfpreis.toFixed(2)}">
              ${Array.from({ length: 10 }, (_, i) => `<option value="${
                i + 1
              }"${menge === i + 1 ? " selected" : ""}>${i + 1}</option>`).join("")}
            </select>

          </td>
          <td>${einzelfpreis.toFixed(2)} €</td>
          <td class="summe-cell">${gesamt.toFixed(2)} €</td>
          <td>
            <button class="remove-btn" data-id="${
              item.product_id
            }" data-size="${item.size}" data-name="${item.name}" data-image="${
          item.image_main
        }">❌</button>
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
          const name = btn.dataset.name;
          const image = btn.dataset.image;
          removeFromCart(id, size, { name, image, productId: id });
        });
      });

      // 🆙 Menge ändern
      document.querySelectorAll(".qty-select").forEach((sel) => {
        sel.addEventListener("change", () => {
          let qty = parseInt(sel.value);

          if (!qty || qty < 1) {
            qty = 1;
            sel.value = 1;
          }
          const price = parseFloat(sel.dataset.price) || 0;
          const sumCell = sel.closest("tr").querySelector(".summe-cell");
          if (sumCell) sumCell.textContent = `${(price * qty).toFixed(2)} €`;
          recalculateTotals();
          const id = sel.dataset.id;
          const size = sel.dataset.size;

          updateCartQuantity(id, size, qty, false);
        });
      });
    });
}
function removeFromCart(productId, size, previewData = null) {
  fetch("index.php?page=cart&action=remove", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `id=${encodeURIComponent(productId)}&size=${encodeURIComponent(
      size
    )}`,
  })
    .then(() => {
      if (previewData) {
        zeigeCartRemovePreview({
          name: previewData.name,
          image: previewData.image,
          productId: previewData.productId || productId,
        });
      }
      loadList();
      updateCartCount();
    })
    .catch((err) => console.error("Fehler beim Entfernen:", err));
}

function updateCartQuantity(productId, size, quantity, reload = true) {
  fetch("index.php?page=cart&action=update", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `id=${encodeURIComponent(productId)}&size=${encodeURIComponent(
      size
    )}&quantity=${encodeURIComponent(quantity)}`,
  })
    .then(() => {
      if (reload) loadList();
      updateCartCount();
    })
    .catch((err) => console.error("Fehler beim Aktualisieren:", err));
}

function recalculateTotals() {
  const sumCells = document.querySelectorAll(".summe-cell");
  const zwischensummeEl = document.getElementById("zwischensumme");
  const gesamtsummeEl = document.getElementById("gesamtsumme");
  const nettoEl = document.getElementById("nettosumme");
  const mwstEl = document.getElementById("mwstbetrag");
  let total = 0;
  sumCells.forEach((cell) => {
    const val = parseFloat(cell.textContent);
    if (!isNaN(val)) total += val;
  });
  const netto = total / 1.19;
  const mwst = total - netto;
  if (zwischensummeEl) zwischensummeEl.textContent = `${total.toFixed(2)} €`;
  if (gesamtsummeEl) gesamtsummeEl.textContent = `${total.toFixed(2)} €`;
  if (nettoEl) nettoEl.textContent = `${netto.toFixed(2)} €`;
  if (mwstEl) mwstEl.textContent = `${mwst.toFixed(2)} €`;
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
function zeigeGestapeltesPopup({
  name,
  image,
  message,
  productId = null,
  icon = "🔔",
  timeout = 4000,
  buttons = "",
  onInit = null,
}) {
  const stack = document.getElementById("popup-stack");
  if (!stack) return;

  const popup = document.createElement("div");
  popup.className = "popup-instance";

  popup.innerHTML = `
    <div class="popup-content-flex">
      <img src="${image}" alt="${name}" />
      <div class="popup-text-info">
        <strong>${name}</strong>
        <small>${icon} ${message}</small>
        <div class="popup-buttons">
          ${
            buttons ||
            (productId
              ? `<a href="index.php?page=product&action=detail&id=${productId}">🔍 Anzeigen</a>`
              : "")
          }
        </div>
      </div>
    </div>
  `;

  // Neue Popups oben einfügen, damit ältere nach unten wandern
  stack.prepend(popup);

  if (typeof onInit === "function") {
    onInit(popup);
  }

  setTimeout(() => {
    popup.classList.add("fade-out");
    setTimeout(() => popup.remove(), 400);
  }, timeout);
}

function zeigeProduktPreview({ name, image, price, productId }) {
  const popup = document.getElementById("cart-preview-popup");
  if (!popup) return;

  popup.innerHTML = `
    <div class="popup-content-flex">
      <img src="${image}" alt="${name}" />
      <div class="popup-text-info">
        <strong>${name}</strong>
        <small>🛒 In den Warenkorb gelegt</small>
        <small>${price.toFixed(2)} €</small>
        <div class="popup-buttons">
          <a href="index.php?page=cart&action=view">Warenkorb</a>
          ${
            !isOnProductDetailPageCart
              ? `<a href="index.php?page=product&action=detail&id=${productId}">Anzeigen</a>`
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

function zeigeCartRemovePreview({ name, image, productId }) {
  const isDetailPage =
    location.href.includes("page=product") &&
    location.href.includes("action=detail");

  zeigeGestapeltesPopup({
    name,
    image,
    message: "Aus dem Warenkorb entfernt",
    productId,
    icon: "❌",
    buttons: `
      <a href="index.php?page=cart&action=view">Warenkorb</a>
      ${
        !isDetailPage
          ? `<a href="index.php?page=product&action=detail&id=${productId}" class="show-btn">Anzeigen</a>`
          : ""
      }
    `,
  });
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
    })
    .catch((err) => {
      console.error("Fehler beim Checkout:", err);
    });
}
