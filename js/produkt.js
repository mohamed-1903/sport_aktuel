// Sammlung gültiger Rabattcodes und deren Nachlass in Prozent
const DISCOUNT_CODES = {
  12345: 10,
  54321: 15,
  SP_20: 20,
  "00000": 5,
};

// Sobald der DOM geladen ist, wird für jedes Produkt die
// Initialisierung ausgeführt
document.addEventListener("DOMContentLoaded", () => {
  document
    .querySelectorAll("[data-product-index]")
    .forEach((section) => setupProduct(section));
});

function setupProduct(section) {
  const idx = section.dataset.productIndex;

  // Relevante Elemente innerhalb des Produktbereichs sammeln
  const qtyInput = section.querySelector(`#quantity-${idx}`);
  const mainImage = section.querySelector(`#main-image-${idx}`);
  const additionalImages = section.querySelectorAll(".additional-images img");
  const toggleInfo = section.querySelector(`#toggle-info-${idx}`);
  const desc = section.querySelector(`#description-full-${idx}`);
  const zoomContainer = section.querySelector(`#zoomContainer-${idx}`);

  // Daten für Zoom-Ansicht speichern
  section._zoomData = { currentIndex: 0 };

  // Vorschaubilder wechseln das Hauptbild
  additionalImages.forEach((img, i) =>
    img.addEventListener("click", () => changeImage(section, img.src, i))
  );

  // Klick aufs Hauptbild öffnet die Zoom-Ansicht
  mainImage.addEventListener("click", () => openZoomModal(section));

  // Ein-/Ausklappen der Produktbeschreibung
  if (toggleInfo) {
    toggleInfo.addEventListener("click", () => toggleDescription(section));
  }

  // Zoom-Interaktion im Produktbild
  zoomContainer.addEventListener("mouseenter", () => enableZoom(section));
  zoomContainer.addEventListener("mouseleave", () => disableZoom(section));
  zoomContainer.addEventListener("mousemove", (e) => moveZoom(section, e));

  // Preis neu berechnen, sobald Eingaben geändert werden
  qtyInput.addEventListener("input", () => updateDisplay(section));
  const giftWrapEl = section.querySelector(`#giftWrap-${idx}`);
  const pinInputEl = section.querySelector(`#pin-${idx}`);
  giftWrapEl?.addEventListener("change", () => updateDisplay(section));
  pinInputEl?.addEventListener("input", () => updateDisplay(section));

  // Anzeige initial updaten
  updateDisplay(section);
  // Elemente für den Produktvergleich
  const compareBtn = document.getElementById("showCompareForm");
  if (!compareBtn) return;

  const form = document.getElementById("compareForm");
  const select = document.getElementById("compareSelect");
  const submit = document.getElementById("compareSubmit");

  // Formular zum Produktvergleich ein-/ausblenden
  compareBtn.addEventListener("click", () => {
    form.classList.toggle("hidden");
  });

  // Bei Bestätigung auf die Detailseite mit beiden IDs springen
  submit.addEventListener("click", () => {
    const otherId = select.value;
    if (!otherId) return;
    const currentId = compareBtn.dataset.currentId;
    window.location.href = `index.php?page=product&action=detail&id=${currentId}&id2=${otherId}`;
  });
}

// Tauscht das Hauptbild aus und merkt sich die aktuelle Position
function changeImage(section, src, index) {
  const idx = section.dataset.productIndex;
  const mainImage = section.querySelector(`#main-image-${idx}`);
  const images = section.querySelectorAll(".additional-images img");

  mainImage.src = src;
  images.forEach((img, i) => img.classList.toggle("selected", i === index));

  section._zoomData.currentIndex = index;

  const container = section.querySelector(`#zoomContainer-${idx}`);
  if (container.classList.contains("zoomed")) {
    container.style.backgroundImage = `url('${src}')`;
  }
}

// Zeigt oder versteckt die vollständige Produktbeschreibung
function toggleDescription(section) {
  const idx = section.dataset.productIndex;
  const desc = section.querySelector(`#description-full-${idx}`);
  const icon = section.querySelector(`#toggle-info-${idx} .toggle-icon`);
  const hidden = desc.classList.toggle("hidden");
  if (icon) icon.textContent = hidden ? "+" : "-";
}
// Aufpreis für Geschenkverpackung ermitteln
function getGiftWrapCharge(section) {
  const idx = section.dataset.productIndex;
  const checkbox = section.querySelector(`#giftWrap-${idx}`);
  return checkbox?.checked ? 2 : 0;
}

// Basispreis des Produkts (ohne Rabatt)
function getBasePrice(section) {
  return parseFloat(section.dataset.basePrice || 0);
}
// Vom Admin gesetzten Rabatt auslesen
function getAdminDiscount(section) {
  return parseFloat(section.dataset.adminDiscount || 0);
}
// Preis nach Adminrabatt berechnen
function getSalePrice(section) {
  const base = getBasePrice(section);
  const d = getAdminDiscount(section);
  return d > 0 ? base * (1 - d / 100) : base;
}
// Nicht verwendet: berechnet den Gesamtpreis inkl. Geschenkoption
function calculateBasePrice(section) {
  const base = getSalePrice(section); // brutto Basis nach Adminrabatt
  const giftCharge = getGiftWrapCharge(section);
  const qty =
    parseInt(
      section.querySelector(`#quantity-${section.dataset.productIndex}`).value
    ) || 1;
  return (base + giftCharge) * qty;
}

// Berechnet Endpreis und angewandten Rabatt für die aktuellen Eingaben
function calculatePrice(section) {
  const idx = section.dataset.productIndex;
  const qty = parseInt(section.querySelector(`#quantity-${idx}`).value) || 1;
  const gift = section.querySelector(`#giftWrap-${idx}`)?.checked;
  const pin = section.querySelector(`#pin-${idx}`)?.value.trim() || "";
  let subtotal = getSalePrice(section) * qty;
  if (gift) subtotal += 2;

  // Rabattcode prüfen
  let discount = 0;
  if (pin.length >= 5 && DISCOUNT_CODES.hasOwnProperty(pin)) {
    discount = DISCOUNT_CODES[pin];
  }
  const original = subtotal;
  const final = discount > 0 ? original * (1 - discount / 100) : original;
  return { original, final, discount };
}

// Aktualisiert die Preis­- und Rabatt-Anzeige im DOM
function updateDisplay(section) {
  const idx = section.dataset.productIndex;
  const { original, final, discount } = calculatePrice(section);
  const finalValueEl = section.querySelector(`#finalPriceValue-${idx}`);
  const originalPriceEl = section.querySelector(`#original-price-${idx}`);
  const discountLabelEl = section.querySelector(`#discountLabel-${idx}`);
  const basePriceEl = section.querySelector(`#basePrice-${idx}`);
  const infoEl = section.querySelector(`#rabatt-info-${idx}`);

  if (discount > 0) {
    originalPriceEl.style.display = "inline";
    originalPriceEl.textContent = `${original.toFixed(2)}€ inkl. Mwst.`;
    originalPriceEl.style.textDecoration = "line-through";
    discountLabelEl.style.display = "inline";
    discountLabelEl.textContent = `-${discount}%`;
    if (infoEl) {
      infoEl.textContent = `✔ Rabattcode akzeptiert: ${discount}%`;
      infoEl.style.color = "green";
    }
  } else {
    originalPriceEl.style.display = "none";
    if (!basePriceEl || basePriceEl.style.display === "none") {
      discountLabelEl.style.display = "none";
    }
    if (infoEl) {
      if (section.querySelector(`#pin-${idx}`).value.trim().length >= 5) {
        infoEl.textContent = "❌ Ungültiger PIN-Code.";
        infoEl.style.color = "red";
      } else {
        infoEl.textContent = "";
      }
    }
  }
  finalValueEl.textContent = `${final.toFixed(2)}€ inkl. Mwst.`;

  const list = section.querySelector(".price-breakdown");
  if (list) {
    const gift = getGiftWrapCharge(section);
    const qty = parseInt(section.querySelector(`#quantity-${idx}`).value) || 1;
    const discountAmount =
      (getSalePrice(section) + gift) * qty * (discount / 100);
    const parts = [];
    if (gift) parts.push(`+${gift.toFixed(2)} €`);
    if (discount) parts.push(`-${discountAmount.toFixed(2)} €`);
    list.textContent = parts.join(" ");
  }
}

// ---- Zoom Handling ----
// Aktuell geöffnete Sektion (wird derzeit nicht weiter verwendet)
let currentSection = null;
// Bildquellen für die Zoom-Galerie
let zoomImages = [];
let currentImageIndex = 0;
let zoomScale = 1;

// Öffnet das Bild in einer Modal-Galerie
function openZoomModal(section) {
  currentSection = section;
  const idx = section.dataset.productIndex;
  const thumbs = [...section.querySelectorAll(".additional-images img")];
  zoomImages = thumbs.map((img) => img.src);
  currentImageIndex = section._zoomData.currentIndex || 0;
  zoomScale = 1;

  const zoomImage = document.getElementById("zoom-image");
  zoomImage.src = zoomImages[currentImageIndex];
  zoomImage.style.transform = `scale(${zoomScale})`;

  document.body.classList.add("modal-open");
  document.getElementById("zoomModal").classList.remove("hidden");
}

// Schliesst die Zoom-Galerie wieder
function closeZoomModal() {
  document.body.classList.remove("modal-open");
  document.getElementById("zoomModal").classList.add("hidden");
}

// Nächstes Bild in der Zoom-Ansicht anzeigen
function nextZoomImage() {
  currentImageIndex = (currentImageIndex + 1) % zoomImages.length;
  updateZoomImage();
}

// Vorheriges Bild in der Zoom-Ansicht anzeigen
function prevZoomImage() {
  currentImageIndex =
    (currentImageIndex - 1 + zoomImages.length) % zoomImages.length;
  updateZoomImage();
}

// Bild vergrößern
function zoomIn() {
  zoomScale = Math.min(3, zoomScale + 0.25);
  updateZoomImage();
}

// Bild verkleinern
function zoomOut() {
  zoomScale = Math.max(0.5, zoomScale - 0.25);
  updateZoomImage();
}

// Zoom zurücksetzen
function resetZoom() {
  zoomScale = 1;
  updateZoomImage();
}

// Aktuelles Zoom-Bild und Skalierung setzen
function updateZoomImage() {
  const zoomImage = document.getElementById("zoom-image");
  zoomImage.src = zoomImages[currentImageIndex];
  zoomImage.style.transform = `scale(${zoomScale})`;
}

// 🎯 Tastatursteuerung fürs Modal
// Unterstützt Navigation und Zoom per Tastatur
document.addEventListener("keydown", (e) => {
  const modal = document.getElementById("zoomModal");
  if (!modal || modal.classList.contains("hidden")) return;

  switch (e.key) {
    case "ArrowRight":
      nextZoomImage();
      break;
    case "ArrowLeft":
      prevZoomImage();
      break;
    case "Escape":
      closeZoomModal();
      break;
    case "+":
      zoomIn();
      break;
    case "-":
    case "_":
      zoomOut();
      break;
    case "r":
    case "R":
      resetZoom();
      break;
  }
});
// ✨ Klick außerhalb vom Content schließt das Modal
const zoomModalEl = document.getElementById("zoomModal");
if (zoomModalEl) {
  // Klick auf den Hintergrund schließt die Modalansicht
  zoomModalEl.addEventListener("click", (e) => {
    const content = document.getElementById("zoomModalContent");
    if (content && !content.contains(e.target)) {
      closeZoomModal();
    }
  });
}

// Aktiviert den Hover-Zoom auf dem Hauptbild
function enableZoom(section) {
  const idx = section.dataset.productIndex;
  const container = section.querySelector(`#zoomContainer-${idx}`);
  const img = section.querySelector(`#main-image-${idx}`);
  container.classList.add("zoomed");
  container.style.backgroundImage = `url('${img.src}')`;
  container.style.backgroundSize = `150%`;
  container.style.backgroundPosition = `center center`;
}
// Deaktiviert den Hover-Zoom
function disableZoom(section) {
  const idx = section.dataset.productIndex;
  const container = section.querySelector(`#zoomContainer-${idx}`);
  container.classList.remove("zoomed");
  container.style.backgroundImage = "";
  container.style.backgroundSize = "";
  container.style.backgroundPosition = "";
}
// Verschiebt den Hintergrund entsprechend der Mausposition
function moveZoom(section, e) {
  const container = section.querySelector(
    `#zoomContainer-${section.dataset.productIndex}`
  );
  const rect = container.getBoundingClientRect();
  const x = ((e.clientX - rect.left) / rect.width) * 100;
  const y = ((e.clientY - rect.top) / rect.height) * 100;
  container.style.backgroundPosition = `${x}% ${y}%`;
}

// ----- Steuerrechner -----
// Ausgabe des Bruttopreises für ein einzelnes Eingabefeld
// Wird auf der Produktdetailseite verwendet
window.zeigePreis = function () {
  const nettoInput = document.getElementById("netto");
  const ergebnisEl = document.getElementById("bruttoErgebnis");

  if (!nettoInput || !ergebnisEl) return;

  const netto = parseFloat(nettoInput.value);
  if (!isNaN(netto)) {
    const brutto = getTotalPrice(netto).toFixed(2);
    ergebnisEl.innerText = `Preis mit 19% Steuer: ${brutto} €`;
  } else {
    ergebnisEl.innerText = "Bitte gültigen Netto-Preis eingeben.";
  }
};

// Berechnet einen Bruttopreis aus einem Netto-Preis
// Kann universell für andere Eingaben genutzt werden
window.getTotalPrice = function (priceWOTax) {
  const TAX_RATE = 0.19;
  return priceWOTax * (1 + TAX_RATE);
};

// Animation aus anderen Skripten genutzt
// Lässt ein Symbol von einem Start-Element zum Ziel fliegen
function flyToTarget(startEl, targetSelector) {
  const target = document.querySelector(targetSelector);
  if (!startEl || !target) return;

  const startRect = startEl.getBoundingClientRect();
  const endRect = target.getBoundingClientRect();

  const symbol = startEl.textContent.trim();
  const clone = document.createElement("div");
  clone.classList.add("fly-to-target");
  clone.textContent = symbol;

  const startX = startRect.left + startRect.width / 2;
  const startY = startRect.top + startRect.height / 2;
  const endX = endRect.left + endRect.width / 2;
  const endY = endRect.top + endRect.height / 2;
  const dx = endX - startX;
  const dy = endY - startY;

  clone.style.position = "fixed";
  clone.style.left = `${startX}px`;
  clone.style.top = `${startY}px`;
  clone.style.transform = `translate(0, 0)`;
  clone.style.setProperty("--fly-transform", `translate(${dx}px, ${dy}px)`);

  document.body.appendChild(clone);

  requestAnimationFrame(() => {
    clone.classList.add("fly-to-target-anim");
  });

  clone.addEventListener("animationend", () => {
    clone.remove();
    if (target) {
      target.classList.add("pulse-highlight");
      setTimeout(() => target.classList.remove("pulse-highlight"), 1000);
    }
  });
}
// Setzt alle benutzeränderten Felder zurück
function resetFields(section) {
  const idx = section.dataset.productIndex;

  section.querySelector(`#pin-${idx}`).value = "";
  section.querySelector(`#rabatt-info-${idx}`).textContent = "";
  section.querySelector(`#giftWrap-${idx}`).checked = false;
  section.querySelector(`#quantity-${idx}`).value = 1;

  const finalValueEl = section.querySelector(`#finalPriceValue-${idx}`);
  const originalPriceEl = section.querySelector(`#original-price-${idx}`);
  const discountLabelEl = section.querySelector(`#discountLabel-${idx}`);
  const basePriceEl = section.querySelector(`#basePrice-${idx}`);
  const adminDiscount = getAdminDiscount(section);

  finalValueEl.textContent = "";
  originalPriceEl.style.display = "none";
  if (adminDiscount > 0) {
    basePriceEl.style.display = "block";
    discountLabelEl.style.display = "inline";
  } else {
    basePriceEl.style.display = "none";
    discountLabelEl.style.display = "none";
  }

  updateDisplay(section);
}

// Nicht verwendet: zeigt nur einen Preis ohne Rabatt an
function resetFinalPriceDisplay(price, section) {
  const idx = section.dataset.productIndex;
  section.querySelector(`#original-price-${idx}`).style.display = "none";
  section.querySelector(`#discountLabel-${idx}`).style.display = "none";
  section.querySelector(
    `#finalPriceValue-${idx}`
  ).textContent = `${price.toFixed(2)}€ inkl. Mwst.`;
}
