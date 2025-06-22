const DISCOUNT_CODES = {
  12345: 10,
  54321: 15,
  11111: 20,
  "00000": 5,
};

// Initialisierung pro Produktcontainer
document.addEventListener("DOMContentLoaded", () => {
  document
    .querySelectorAll("[data-product-index]")
    .forEach((section) => setupProduct(section));
});

function setupProduct(section) {
  const idx = section.dataset.productIndex;

  const qtyInput = section.querySelector(`#quantity-${idx}`);
  const mainImage = section.querySelector(`#main-image-${idx}`);
  const additionalImages = section.querySelectorAll(".additional-images img");
  const toggleInfo = section.querySelector(`#toggle-info-${idx}`);
  const desc = section.querySelector(`#description-full-${idx}`);
  const zoomContainer = section.querySelector(`#zoomContainer-${idx}`);

  section._zoomData = { currentIndex: 0 };

  additionalImages.forEach((img, i) =>
    img.addEventListener("click", () => changeImage(section, img.src, i))
  );

  mainImage.addEventListener("click", () => openZoomModal(section));

  if (toggleInfo) {
    toggleInfo.addEventListener("click", () => toggleDescription(section));
  }

  zoomContainer.addEventListener("mouseenter", () => enableZoom(section));
  zoomContainer.addEventListener("mouseleave", () => disableZoom(section));
  zoomContainer.addEventListener("mousemove", (e) => moveZoom(section, e));

  qtyInput.addEventListener("input", () => updateDisplay(section));
  const giftWrapEl = section.querySelector(`#giftWrap-${idx}`);
  const pinInputEl = section.querySelector(`#pin-${idx}`);

  giftWrapEl?.addEventListener("change", () => updateDisplay(section));
  pinInputEl?.addEventListener("input", () => updateDisplay(section));

  updateDisplay(section);
}

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

function toggleDescription(section) {
  const idx = section.dataset.productIndex;
  const desc = section.querySelector(`#description-full-${idx}`);
  const icon = section.querySelector(`#toggle-info-${idx} .toggle-icon`);
  const hidden = desc.classList.toggle("hidden");
  if (icon) icon.textContent = hidden ? "+" : "-";
}

function getBasePrice(section) {
  const idx = section.dataset.productIndex;
  const el = section.querySelector(`#basePrice-${idx}`);
  return parseFloat(el?.textContent) || 0;
}

function calculatePrice(section) {
  const idx = section.dataset.productIndex;
  const qty = parseInt(section.querySelector(`#quantity-${idx}`).value) || 1;
  const gift = section.querySelector(`#giftWrap-${idx}`)?.checked;
  const pin = section.querySelector(`#pin-${idx}`)?.value.trim() || "";

  let subtotal = getBasePrice(section) * qty;
  if (gift) subtotal += 2;

  const discountPercent = DISCOUNT_CODES[pin] || 0;
  const discounted = subtotal * (1 - discountPercent / 100);
  return { original: subtotal, final: discounted, discount: discountPercent };
}

function updateDisplay(section) {
  const idx = section.dataset.productIndex;
  const { original, final, discount } = calculatePrice(section);
  const finalValueEl = section.querySelector(`#finalPriceValue-${idx}`);
  const originalPriceEl = section.querySelector(`#original-price-${idx}`);
  const discountLabelEl = section.querySelector(`#discountLabel-${idx}`);
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
    discountLabelEl.style.display = "none";
    if (infoEl) {
      if (document.getElementById("pin").value.trim().length >= 5) {
        infoEl.textContent = "❌ Ungültiger PIN-Code.";
        infoEl.style.color = "red";
      } else {
        infoEl.textContent = "";
      }
    }
  }
  finalValueEl.textContent = `${final.toFixed(2)}€ inkl. Mwst.`;
}

// ---- Zoom Handling ----
let currentSection = null;
let zoomImages = [];
let currentImageIndex = 0;
let zoomScale = 1;

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

function closeZoomModal() {
  document.body.classList.remove("modal-open");
  document.getElementById("zoomModal").classList.add("hidden");
}

function nextZoomImage() {
  currentImageIndex = (currentImageIndex + 1) % zoomImages.length;
  updateZoomImage();
}

function prevZoomImage() {
  currentImageIndex =
    (currentImageIndex - 1 + zoomImages.length) % zoomImages.length;
  updateZoomImage();
}

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

// 🎯 Tastatursteuerung fürs Modal
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
document.getElementById("zoomModal").addEventListener("click", (e) => {
  const content = document.getElementById("zoomModalContent");
  if (!content.contains(e.target)) {
    closeZoomModal();
  }
});

function enableZoom(section) {
  const idx = section.dataset.productIndex;
  const container = section.querySelector(`#zoomContainer-${idx}`);
  const img = section.querySelector(`#main-image-${idx}`);
  container.classList.add("zoomed");
  container.style.backgroundImage = `url('${img.src}')`;
  container.style.backgroundSize = `150%`;
  container.style.backgroundPosition = `center center`;
}
function disableZoom(section) {
  const idx = section.dataset.productIndex;
  const container = section.querySelector(`#zoomContainer-${idx}`);
  container.classList.remove("zoomed");
  container.style.backgroundImage = "";
  container.style.backgroundSize = "";
  container.style.backgroundPosition = "";
}
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
function zeigePreis() {
  const nettoInput = document.getElementById("netto");
  const ergebnisEl = document.getElementById("bruttoErgebnis");

  const nettoPreis = parseFloat(nettoInput.value);
  if (!isNaN(nettoPreis)) {
    const brutto = getTotalPrice(nettoPreis).toFixed(2);
    ergebnisEl.innerText = `Preis mit 19% Steuer: ${brutto} €`;
  } else {
    ergebnisEl.innerText = "Bitte einen gültigen Preis eingeben.";
  }
}

// Animation aus anderen Skripten genutzt
function flyToTarget(startEl, targetSelector, symbol = "❤️") {
  const target = document.querySelector(targetSelector);
  if (!startEl || !target) return;

  const startRect = startEl.getBoundingClientRect();
  const endRect = target.getBoundingClientRect();

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
    target.classList.add("pulse-highlight");
    setTimeout(() => target.classList.remove("pulse-highlight"), 1000);
  });
}

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

  finalValueEl.textContent = "";
  originalPriceEl.style.display = "none";
  discountLabelEl.style.display = "none";
  basePriceEl.style.display = "none";

  updateDisplay(section);
}
function resetFinalPriceDisplay(price, section) {
  const idx = section.dataset.productIndex;
  section.querySelector(`#original-price-${idx}`).style.display = "none";
  section.querySelector(`#discountLabel-${idx}`).style.display = "none";
  section.querySelector(
    `#finalPriceValue-${idx}`
  ).textContent = `${price.toFixed(2)}€ inkl. Mwst.`;
}
document.addEventListener("DOMContentLoaded", () => {
  const compareBtn = document.getElementById("showCompareForm");
  if (!compareBtn) return;

  const form = document.getElementById("compareForm");
  const select = document.getElementById("compareSelect");
  const submit = document.getElementById("compareSubmit");

  compareBtn.addEventListener("click", () => {
    form.classList.toggle("hidden");
  });

  submit.addEventListener("click", () => {
    const otherId = select.value;
    if (!otherId) return;
    const currentId = compareBtn.dataset.currentId;
    window.location.href = `index.php?page=product&action=detail&id=${currentId}&id2=${otherId}`;
  });
});
