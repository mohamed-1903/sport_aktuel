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
          const showBtn = !isOnProductDetailPageCart
            ? `<a href="index.php?page=product&action=detail&id=${iid}">🔍 Anzeigen</a>`
            : "";
          const buttons = `
            ${showBtn}
            <button class="remove-btn">🗑️ Entfernen</button>
            <a href="index.php?page=cart&action=view">🛒 Warenkorb</a>
          `;
          zeigeGestapeltesPopup({
            name,
            image,
            message: `In den Warenkorb gelegt (${size}, ${qty}x)`,
            productId: iid,
            icon: "🛒",
            buttons,
            onInit: (popup) => {
              const rm = popup.querySelector(".remove-btn");
              if (rm) {
                rm.addEventListener("click", () => {
                  removeFromCart(iid, size, { name, image });
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
          removeFromCart(id, size, { name, image });
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
      if (previewData) zeigeCartRemovePreview(previewData);
      loadList();
      updateCartCount();
    })
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
function zeigeGestapeltesPopup({
  name,
  image,
  message,
  productId = null,
  icon = "🔔",
  buttons = "",
  onInit = null,
  timeout = 4000,
}) {
  const stack = document.getElementById("popup-stack");
  if (!stack) return;

  const popup = document.createElement("div");
  popup.className = "popup-instance";

  const btnHTML =
    buttons ||
    (productId
      ? `<a href="index.php?page=product&action=detail&id=${productId}">🔍 Anzeigen</a>`
      : "");

  popup.innerHTML = `
    <div class="popup-content-flex">
      <img src="${image}" alt="${name}" />
      <div class="popup-text-info">
        <strong>${name}</strong>
        <small>${icon} ${message}</small>
        <div class="popup-buttons">${btnHTML}</div>
      </div>
    </div>
  `;

  // Neue Popups oben einfügen, damit ältere nach unten wandern
  stack.prepend(popup);

  if (typeof onInit === "function") {
    try {
      onInit(popup);
    } catch (err) {
      console.error("Popup onInit error", err);
    }
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
          <a href="index.php?page=cart&action=view">Zum Warenkorb</a>
          ${
            !isOnProductDetailPageCart
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
          })
          .catch((err) => {
            console.error("Fehler beim Checkout:", err);
          });
      }