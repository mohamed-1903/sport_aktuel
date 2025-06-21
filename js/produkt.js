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
  document.getElementById("giftWrap")?.addEventListener("change", () =>
    updateDisplay(section)
  );
  document.getElementById("pin")?.addEventListener("input", () =>
    updateDisplay(section)
  );

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
  const gift = document.getElementById("giftWrap")?.checked;
  const pin = document.getElementById("pin")?.value.trim() || "";

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
  const infoEl = document.getElementById("rabatt-info");

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
  currentImageIndex = (currentImageIndex - 1 + zoomImages.length) % zoomImages.length;
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
  const container = section.querySelector(`#zoomContainer-${section.dataset.productIndex}`);
  const rect = container.getBoundingClientRect();
  const x = ((e.clientX - rect.left) / rect.width) * 100;
  const y = ((e.clientY - rect.top) / rect.height) * 100;
  container.style.backgroundPosition = `${x}% ${y}%`;
}

// ----- Steuerrechner -----
function getTotalPrice(priceWOTax) {
  return priceWOTax * (1 + 0.19);
}
function zeigePreis() {
  const nettoPreis = parseFloat(document.getElementById("netto").value);
  if (!isNaN(nettoPreis)) {
    const brutto = getTotalPrice(nettoPreis).toFixed(2);
    document.getElementById("bruttoErgebnis").innerText = `Preis mit 19% Steuer: ${brutto} €`;
  } else {
    document.getElementById("bruttoErgebnis").innerText = "Bitte einen gültigen Preis eingeben.";
  }
}

// Animation aus anderen Skripten genutzt
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

  clone.addEventListener("animationend", () => clone.remove());
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
