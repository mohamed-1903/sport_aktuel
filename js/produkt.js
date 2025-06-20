// Konstanten und Variablen
const TAX_RATE = 0.19; // Mehrwertsteuersatz (19 %)
let subtotal = getBasePrice(); // Basispreis (ohne Rabatte) wird initial geladen
const DISCOUNT_CODES = {
  // Gültige Rabattcodes mit zugehörigem Rabatt in Prozent
  12345: 10,
  54321: 15,
  11111: 20,
  "00000": 5,
};

let quantity = 1; // Standardmenge
let giftWrap = false; // Geschenkverpackung standardmäßig deaktiviert
let appliedCode = ""; // Eingegebener Rabattcode (leer zu Beginn)

// DOM-Elemente referenzieren
const finalValueEl = document.getElementById("finalPriceValue"); // Anzeige: Endpreis
const originalPriceEl = document.getElementById("original-price"); // Anzeige: Ursprünglicher Preis (durchgestrichen)
const discountLabelEl = document.getElementById("discountLabel"); // Anzeige: Rabattlabel (z. B. -10%)
const infoEl = document.getElementById("rabatt-info"); // Anzeige: Hinweis bei Rabattcode
const qtyInput = document.getElementById("quantity"); // Eingabefeld für Menge
const giftCheckbox = document.getElementById("giftWrap"); // Checkbox für Geschenkverpackung
const pinInput = document.getElementById("pin"); // Eingabefeld für Rabattcode (PIN)
const applyBtn = document.getElementById("apply-discount"); // Button zur Rabattcode-Anwendung (nicht genutzt im Code)

// Hauptbild beim Klick auf ein Vorschaubild ändern
function changeImage(imageSrc, index) {
  const mainImage = document.getElementById("main-image"); // Hauptbild
  const additionalImages = document.querySelectorAll(".additional-images img"); // Alle Vorschaubilder

  mainImage.src = imageSrc; // Hauptbild ändern

  additionalImages.forEach((img, i) => {
    img.classList.toggle("selected", i === index - 1); // Bildauswahl visuell hervorheben
  });
}

document
  .getElementById("toggle-info")
  .addEventListener("click", toggleDescription);

// Beschreibung ein-/ausklappen
function toggleDescription() {
  const desc = document.getElementById("description-full");
  const icon = document.querySelector("#toggle-info .toggle-icon");

  const isHidden = desc.classList.toggle("hidden");
  icon.textContent = isHidden ? "+" : "-";
}

// Info-Sektion ein-/ausklappen
const toggleInfo = document.getElementById("toggle-info");
const infoContent = document.getElementById("info-content");

toggleInfo.addEventListener("click", () => {
  infoContent.classList.toggle("show"); // Sichtbarkeit umschalten
});

// Aufpreis für Geschenkverpackung
function getGiftWrapCharge() {
  return document.getElementById("giftWrap").checked ? 2 : 0; // 2 € wenn aktiv
}

// Bruttoberechnung aus Netto
function getTotalPrice(priceWOTax) {
  const taxRate = 0.19;
  return priceWOTax * (1 + taxRate); // Netto + 19 %
}

// Basispreis mit Geschenk und Menge (ohne Rabatt)
function calculateBasePrice() {
  const raw = document.getElementById("priceWOTax").value.trim(); // Netto-Preisfeld
  const quantity = parseInt(document.getElementById("quantity").value) || 1;
  if (raw === "") return null;

  const priceWOTax = parseFloat(raw);
  if (isNaN(priceWOTax) || priceWOTax <= 0) return null;

  const taxed = getTotalPrice(priceWOTax); // Preis mit Steuer
  const gift = getGiftWrapCharge(); // Geschenkzuschlag
  return (taxed + gift) * quantity;
}

// Hauptfunktion zur Preisberechnung
function calculatePrice() {
  const qty = parseInt(qtyInput.value) || 1;
  const gift = giftCheckbox.checked;
  const pin = pinInput.value.trim();
  let subtotal = getBasePrice() * qty;

  if (gift) subtotal += 2; // Geschenkzuschlag hinzufügen

  const discountPercent = DISCOUNT_CODES[pin] || 0;
  const discounted = subtotal * (1 - discountPercent / 100); // Rabatt anwenden

  return {
    original: subtotal,
    final: discounted,
    discount: discountPercent,
  };
}

// Anzeige aktualisieren je nach Rabatt & Eingabe
function updateDisplay() {
  const { original, final, discount } = calculatePrice();

  if (discount > 0) {
    originalPriceEl.style.display = "inline";
    originalPriceEl.textContent = `${original.toFixed(2)}€ inkl. Mwst.`;
    originalPriceEl.style.textDecoration = "line-through";

    discountLabelEl.style.display = "inline";
    discountLabelEl.textContent = `-${discount}%`;

    infoEl.textContent = `✔ Rabattcode akzeptiert: ${discount}%`;
    infoEl.style.color = "green";
  } else {
    originalPriceEl.style.display = "none";
    discountLabelEl.style.display = "none";

    if (appliedCode.length >= 5) {
      infoEl.textContent = "❌ Ungültiger PIN-Code.";
      infoEl.style.color = "red";
    } else {
      infoEl.textContent = "";
    }
  }

  finalValueEl.textContent = `${final.toFixed(2)}€ inkl. Mwst.`; // Endpreis aktualisieren
}

// Event-Listener beim Laden der Seite
window.addEventListener("DOMContentLoaded", () => {
  updateDisplay(); // Direkt anzeigen

  qtyInput.addEventListener("input", (e) => {
    quantity = parseInt(e.target.value) || 1;
    updateDisplay(); // Menge ändert sich
  });

  giftCheckbox.addEventListener("change", (e) => {
    giftWrap = e.target.checked;
    updateDisplay(); // Geschenkoption ändert sich
  });

  pinInput.addEventListener("input", (e) => {
    appliedCode = e.target.value.trim();
    updateDisplay(); // Rabattcode-Eingabe
  });
});

// Anzeige zurücksetzen auf Anfangswert
function resetFinalPriceDisplay(price) {
  document.getElementById("originalPrice").style.display = "none";
  document.getElementById("discountLabel").style.display = "none";
  document.getElementById("finalPriceValue").textContent = `${price.toFixed(
    2
  )}€ inkl. Mwst.`;
}

// Formularfelder & Anzeige zurücksetzen
function resetFields() {
  document.getElementById("netto").value = "";
  document.getElementById("bruttoErgebnis").textContent = "";

  document.getElementById("pin").value = "";
  document.getElementById("rabatt-info").textContent = "";

  document.getElementById("giftWrap").checked = false;

  document.getElementById("quantity").value = 1;

  document.getElementById("finalPriceValue").textContent = "";
  document.getElementById("original-price").style.display = "none";
  document.getElementById("discountLabel").style.display = "none";
  document.getElementById("basePrice").style.display = "none";
  appliedCode = "";
  quantity = 1;
  giftWrap = false;
  updateDisplay(); // Alles neu anzeigen
}

// Zoomfunktion bei Hover über Hauptbild
(function () {
  const container = document.querySelector(".zoom-bg-container");
  const img = document.getElementById("main-image");
  const zoomFactor = 1.5;

  function enableZoom() {
    container.classList.add("zoomed");
    container.style.backgroundImage = `url('${img.src}')`;
    container.style.backgroundSize = `${zoomFactor * 100}%`;
    container.style.backgroundPosition = `center center`;
  }

  function disableZoom() {
    container.classList.remove("zoomed");
    container.style.backgroundImage = "";
    container.style.backgroundSize = "";
    container.style.backgroundPosition = "";
  }

  function moveZoom(e) {
    const rect = container.getBoundingClientRect();
    const x = ((e.clientX - rect.left) / rect.width) * 100;
    const y = ((e.clientY - rect.top) / rect.height) * 100;
    container.style.backgroundPosition = `${x}% ${y}%`; // Zoompunkt aktualisieren
  }

  container.addEventListener("mouseenter", enableZoom);
  container.addEventListener("mouseleave", disableZoom);
  container.addEventListener("mousemove", moveZoom);

  const originalChangeImage = window.changeImage;
  window.changeImage = (src, idx) => {
    originalChangeImage(src, idx);
    img.src = src;
    if (container.classList.contains("zoomed")) {
      enableZoom(); // Zoom neu anwenden nach Bildwechsel
    }
  };
})();

// Brutto-Anzeige (nur Steuer) bei Eingabe von Netto
function zeigePreis() {
  const nettoPreis = parseFloat(document.getElementById("netto").value);
  if (!isNaN(nettoPreis)) {
    const brutto = getTotalPrice(nettoPreis).toFixed(2);
    document.getElementById(
      "bruttoErgebnis"
    ).innerText = `Preis mit 19% Steuer: ${brutto} €`;
  } else {
    document.getElementById("bruttoErgebnis").innerText =
      "Bitte einen gültigen Preis eingeben.";
  }
}

// Basispreis (aus verstecktem Element) extrahieren
function getBasePrice() {
  const el = document.getElementById("basePrice");
  if (!el) return 0;
  return parseFloat(el.textContent) || 0;
}
let currentImageIndex = 0;
let zoomImages = [];
let zoomScale = 1;

// Bild wechseln + Index merken
function changeImage(src, index) {
  const mainImage = document.getElementById("main-image");
  mainImage.src = src;
  currentImageIndex = index;
}

// Modal öffnen per Klick auf Hauptbild
document.getElementById("main-image").addEventListener("click", () => {
  const thumbs = [...document.querySelectorAll(".additional-images img")];
  zoomImages = thumbs.map((img) => img.src);
  openZoomModal();
});

// Modal öffnen
function openZoomModal() {
  zoomScale = 1;
  document.body.classList.add("modal-open");
  const zoomImage = document.getElementById("zoom-image");
  zoomImage.src = zoomImages[currentImageIndex];
  zoomImage.style.transform = `scale(${zoomScale})`;
  document.getElementById("zoomModal").classList.remove("hidden");
}

function closeZoomModal() {
  document.body.classList.remove("modal-open");
  document.getElementById("zoomModal").classList.add("hidden");
}

// Bildwechsel
function nextZoomImage() {
  currentImageIndex = (currentImageIndex + 1) % zoomImages.length;
  updateZoomImage();
}
function prevZoomImage() {
  currentImageIndex =
    (currentImageIndex - 1 + zoomImages.length) % zoomImages.length;
  updateZoomImage();
}

// Zoomsteuerung
function zoomIn() {
  zoomScale = Math.min(3, zoomScale + 0.25);
  updateZoomImage();
}

function zoomOut() {
  zoomScale = Math.max(0.5, zoomScale - 0.25);
  updateZoomImage();
}

function resetZoom() {
  zoomScale = 1;
  updateZoomImage();
}
function updateZoomImage() {
  const zoomImage = document.getElementById("zoom-image");
  zoomImage.src = zoomImages[currentImageIndex];
  zoomImage.style.transform = `scale(${zoomScale})`;
}
// ✨ Klick außerhalb vom Content schließt das Modal
document.getElementById("zoomModal").addEventListener("click", (e) => {
  const content = document.getElementById("zoomModalContent");
  if (!content.contains(e.target)) {
    closeZoomModal();
  }
});
// 🎯 Tastatursteuerung fürs Modal
document.addEventListener("keydown", (e) => {
  const modal = document.getElementById("zoomModal");
  if (modal.classList.contains("hidden")) return;

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
function flyToTarget(startEl, targetSelector) {
  const target = document.querySelector(targetSelector);
  if (!startEl || !target) return;

  const startRect = startEl.getBoundingClientRect();
  const endRect = target.getBoundingClientRect();

  const symbol = startEl.textContent.trim();

  const clone = document.createElement("div");
  clone.classList.add("fly-to-target");
  clone.textContent = symbol;

  // exakte Startposition (Mitte von startEl)
  const startX = startRect.left + startRect.width / 2;
  const startY = startRect.top + startRect.height / 2;

  // exakte Zielposition (Mitte von target)
  const endX = endRect.left + endRect.width / 2;
  const endY = endRect.top + endRect.height / 2;

  // Differenz
  const dx = endX - startX;
  const dy = endY - startY;

  clone.style.position = "fixed";
  clone.style.left = `${startX}px`;
  clone.style.top = `${startY}px`;
  clone.style.transform = `translate(0, 0)`; // Start ohne Verschiebung
  clone.style.setProperty("--fly-transform", `translate(${dx}px, ${dy}px)`);

  document.body.appendChild(clone);

  // Animation starten
  requestAnimationFrame(() => {
    clone.classList.add("fly-to-target-anim");
  });

  clone.addEventListener("animationend", () => clone.remove());
  // Animation starten
  requestAnimationFrame(() => {
    clone.classList.add("fly-to-target-anim");
  });

  // Beim Ankommen entfernen + Ziel aufblinken
  clone.addEventListener("animationend", () => {
    clone.remove();
    if (target) {
      target.classList.add("pulse-highlight");
      setTimeout(() => target.classList.remove("pulse-highlight"), 1000);
    }
  });
}
