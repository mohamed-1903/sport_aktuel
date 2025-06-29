// ✅ FILTERFUNKTION
let currentPage = 1;
let paginatedItems = [];

function getItemsPerPage() {
  const container = document.getElementById("produktContainer");
  if (!container) return 8;

  if (container.classList.contains("einzelprodukt-grid")) {
    const cols = window
      .getComputedStyle(container)
      .getPropertyValue("grid-template-columns")
      .split(" ")
      .filter((c) => c.trim().length > 0).length;
    return (cols || 1) * 2;
  }

  if (container.classList.contains("einzelprodukt-list")) {
    return 4;
  }

  return 2;
}

window.applyFilter = function () {
  const priceSel = document.getElementById("filter-price");
  let minVal = 0;
  let maxVal = Infinity;
  if (priceSel && priceSel.value) {
    if (priceSel.value.includes("-")) {
      const [mi, ma] = priceSel.value.split("-");
      minVal = parseFloat(mi) || 0;
      maxVal = parseFloat(ma) || Infinity;
    } else if (priceSel.value.endsWith("+")) {
      minVal = parseFloat(priceSel.value) || 0;
    }
  }

  const filterWerte = {
    marke: document.getElementById("filter-marke")?.value || "",
    farbe: document.getElementById("filter-farbe")?.value || "",
    minPreis: isNaN(minVal) ? 0 : minVal,
    maxPreis: isNaN(maxVal) ? Infinity : maxVal,
    mannschaft: document.getElementById("filter-mannschaft")?.value || "",
    geschlecht: document.getElementById("filter-geschlecht")?.value || "",
  };

  document.querySelectorAll(".einzelprodukt").forEach((produkt) => {
    const p = produkt.dataset;
    const preis = parseFloat(p.preis);
    const sichtbar =
      (!filterWerte.marke ||
        (p.marke || "").toLowerCase() === filterWerte.marke.toLowerCase()) &&
      (!filterWerte.farbe ||
        (p.farbe || "").toLowerCase() === filterWerte.farbe.toLowerCase()) &&
      (!filterWerte.mannschaft ||
        (p.mannschaft || "").toLowerCase() ===
          filterWerte.mannschaft.toLowerCase()) &&
      (!filterWerte.geschlecht ||
        (p.geschlecht || "").toLowerCase() ===
          filterWerte.geschlecht.toLowerCase()) &&
      preis >= filterWerte.minPreis &&
      preis <= filterWerte.maxPreis;


    // Sichtbarkeit sowohl per Display-Style als auch Flag merken
    // Das Flag erlaubt der Pagination alle passenden Elemente zu zählen,
    // selbst wenn sie aufgrund der Seitennummer gerade ausgeblendet sind
    produkt.dataset.visible = sichtbar ? "1" : "0";
    // leeres Display lässt die ursprüngliche Flex-Darstellung erhalten
    produkt.style.display = sichtbar ? "" : "none";
  });


  currentPage = 1;
  updatePagination();
  updateActiveFilters();
};

// ✅ PRODUKTSUCHE mit Feedback
function produktSuche() {
  const eingabe = document
    .getElementById("produktsuche")
    ?.value.toLowerCase()
    .trim();
  const feedbackEl = document.getElementById("such-feedback");

  // Nur Feedback-Text anzeigen, keine DOM-Änderung!
  if (!eingabe || eingabe.length < 2) {
    if (feedbackEl) feedbackEl.textContent = "";
    return;
  }

  const treffer = alleProdukte.filter((p) =>
    [p.name, p.marke, p.farbe, p.geschlecht, p.mannschaft]
      .map((s) => (s || "").toLowerCase())
      .join(" ")
      .includes(eingabe)
  );

  if (feedbackEl) {
    feedbackEl.textContent =
      treffer.length === 0
        ? "❌ Keine passenden Produkte gefunden."
        : `✅ ${treffer.length} Produkt${
            treffer.length === 1 ? "" : "e"
          } gefunden.`;
  }
}

// 🔁 FILTER ZURÜCKSETZEN

// ✅ AUTOCOMPLETE & LADEN
let alleProdukte = [];
let fokusIndex = -1;

function initFilterAndSearch() {
  const input = document.getElementById("produktsuche");
  const shadow = document.getElementById("autocomplete-shadow");
  const liste = document.getElementById("such-vorschlaege");

  if (input) {
    input.addEventListener("input", () => {
      produktSuche();
      autocompleteSuche();
    });

    input.addEventListener("keydown", (e) => {
      if (e.key === "Enter" && fokusIndex === -1) {
        e.preventDefault();
        const query = input.value.trim();
        if (query.length > 1) {
          const url = `index.php?page=product&action=search&query=${encodeURIComponent(
            query
          )}`;
          window.location.href = url;
        }
      } else {
        handleTastaturNavigation(e, liste, input, shadow);
      }
    });
  }

  document.addEventListener("click", (e) => {
    if (!e.target.closest(".search-container")) {
      liste.style.display = "none";
      shadow.value = "";
    }
  });

  // Produktdaten laden
  fetch("data/products.json")
    .then((res) => res.json())
    .then((data) => {
      alleProdukte = data.products || [];
    });

  if (typeof produktKonfigurationen !== "undefined") {
    produktKonfigurationen.forEach(({ containerId, urls }) => {
      ladeProdukte(containerId, urls);
    });
  }

  const prodContainer = document.getElementById("produktContainer");
  const savedLayout = localStorage.getItem("productLayout");
  if (prodContainer && savedLayout === "list") {
    prodContainer.classList.add("einzelprodukt-list");
    prodContainer.classList.remove("einzelprodukt-grid");
  }
  updateLayoutToggle(savedLayout === "list" ? "list" : "grid");
  populateFilterOptions();
  const priceSel = document.getElementById("filter-price");
  if (priceSel) priceSel.addEventListener("change", applyFilter);
  updateActiveFilters();

  adjustFilterBar();
  window.addEventListener("resize", adjustFilterBar);

  // alle Filter zurücksetzen und Pagination initial erstellen
  resetFilter();
  updatePagination();
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initFilterAndSearch);
} else {
  initFilterAndSearch();
}


// 🔽 PRODUKTE LADEN
function ladeProdukte(containerId, urls) {
  const container = document.getElementById(containerId);
  if (!container) return;

  Promise.all(
    urls.map((url) =>
      fetch(url)
        .then((res) => res.text())
        .then((html) => {
          const temp = document.createElement("div");
          temp.innerHTML = html;
          Array.from(temp.children).forEach((el) => container.appendChild(el));
        })
    )
  ).then(() => {
    resetFilter();
    produktSuche();
    if (!window.originalProductOrder) {
      window.originalProductOrder = Array.from(
        container.querySelectorAll(".einzelprodukt")
      );
    }
    updatePagination();
  });
}

function autocompleteSuche() {
  const input = document.getElementById("produktsuche");
  const liste = document.getElementById("such-vorschlaege");
  const shadow = document.getElementById("autocomplete-shadow");

  const wert = input.value.toLowerCase().trim();
  liste.innerHTML = "";
  autocompleteVorschlag = "";
  fokusIndex = -1;

  if (wert.length < 1) {
    liste.innerHTML = "";
    liste.style.display = "none";
    shadow.value = "";
    return;
  }

  const treffer = alleProdukte.filter((p) =>
    [p.name, p.marke, p.farbe, p.geschlecht, p.category, p.subcategory]
      .map((s) => (s || "").toLowerCase())
      .join(" ")
      .includes(wert)
  );

  if (treffer.length > 0) {
    const match = treffer.find((p) =>
      (p.name || "").toLowerCase().startsWith(wert)
    );
    shadow.value = match?.name || "";

    // Alle Treffer anzeigen, nicht nur eine begrenzte Anzahl
    treffer.forEach((p) => {
      const name = p.name;
      const price =
        typeof p.priceValue !== "undefined"
          ? parseFloat(p.priceValue).toFixed(2) + " €"
          : p.price || "Preis?";
      const img = p.imageMain || "";

      const li = document.createElement("li");
      li.innerHTML = `
        <img src="${img}" alt="${name}" />
        <div>
          <strong>${name}</strong><br>
          <small>${price}</small>
        </div>
      `;
      li.addEventListener("click", () => {
        const url = `index.php?page=product&action=detail&id=${encodeURIComponent(
          p.iid
        )}`;
        window.location.href = url;
      });

      liste.appendChild(li);
    });
    liste.style.display = "block";
  } else {
    liste.innerHTML = `<li class="keine-treffer-box"><div class="keine-treffer-icon">🔍</div><div><strong>Keine Treffer</strong></div></li>`;
    liste.style.display = "block";
    shadow.value = "";
  }
}

// ⌨️ TASTATUR-NAVIGATION
function handleTastaturNavigation(e, liste, input, shadow) {
  const eintraege = liste.querySelectorAll("li");
  if (!eintraege.length) return;

  if (e.key === "ArrowDown") {
    e.preventDefault();
    fokusIndex = (fokusIndex + 1) % eintraege.length;
  } else if (e.key === "ArrowUp") {
    e.preventDefault();
    fokusIndex = (fokusIndex - 1 + eintraege.length) % eintraege.length;
  } else if (e.key === "Enter" && fokusIndex >= 0) {
    e.preventDefault();
    eintraege[fokusIndex].click();
  } else if (e.key === "Tab" && shadow.value) {
    e.preventDefault();
    input.value = shadow.value;
    shadow.value = "";
    liste.style.display = "none";
    input.dispatchEvent(new Event("input"));
    input.setSelectionRange(input.value.length, input.value.length);
  } else if (e.key === "Escape") {
    liste.style.display = "none";
    shadow.value = "";
  }

  updateFokus(eintraege);
}

function updateFokus(eintraege) {
  eintraege.forEach((li, i) => {
    li.classList.toggle("focused", i === fokusIndex);
    if (i === fokusIndex) li.scrollIntoView({ block: "nearest" });
  });
}

function colorEmoji(name) {
  const base = name.toLowerCase().split(/[-/]/)[0];
  const map = {
    schwarz: "⬛",
    weiss: "⬜",
    weiß: "⬜",
    blau: "🟦",
    rot: "🟥",
    gelb: "🟨",
    grün: "🟩",
    gruen: "🟩",
    grau: "⬜",
    orange: "🟧",
    lila: "🟪",
    violett: "🟪",
    pink: "🩷",
    gold: "🟨",
    braun: "🟫",
    navy: "🟦",
  };
  return map[base] || "⬛";
}

window.populateFilterOptions = function () {
  const container = document.getElementById("produktContainer");
  if (!container) return;

  const products = Array.from(container.querySelectorAll(".einzelprodukt"));
  const collect = (attr) => [
    ...new Set(products.map((p) => p.dataset[attr]).filter(Boolean)),
  ];

  const setOptions = (id, values, label) => {
    const sel = document.getElementById(id);
    if (!sel) return;
    const current = sel.value;
    sel.innerHTML = "";
    const allOpt = document.createElement("option");
    allOpt.value = "";
    allOpt.textContent = label;
    sel.appendChild(allOpt);
    values.forEach((v) => {
      const opt = document.createElement("option");
      opt.value = v;
      if (id === "filter-farbe") {
        opt.textContent = `${colorEmoji(v)} ${v}`;
      } else {
        opt.textContent = v;
      }
      sel.appendChild(opt);
    });
    if (values.includes(current)) sel.value = current;
  };

  setOptions("filter-marke", collect("marke"), "Alle Marken");
  setOptions("filter-farbe", collect("farbe"), "Alle Farben");
  setOptions("filter-mannschaft", collect("mannschaft"), "Alle Mannschaften");
  setOptions("filter-geschlecht", collect("geschlecht"), "Alle Geschlechter");

  const priceSel = document.getElementById("filter-price");
  if (priceSel) {
    const prices = products
      .map((p) => parseFloat(p.dataset.preis) || 0)
      .filter((n) => !isNaN(n));
    const minPrice = Math.min(...prices);
    const maxPrice = Math.max(...prices);
    const step = 50;
    const current = priceSel.value;
    priceSel.innerHTML = "";
    const allOpt = document.createElement("option");
    allOpt.value = "";
    allOpt.textContent = "Alle Preise";
    priceSel.appendChild(allOpt);
    for (let p = Math.floor(minPrice / step) * step; p < maxPrice; p += step) {
      const opt = document.createElement("option");
      if (p + step >= maxPrice) {
        opt.value = `${p}+`;
        opt.textContent = `ab ${p} €`;
        priceSel.appendChild(opt);
        break;
      } else {
        opt.value = `${p}-${p + step}`;
        opt.textContent = `${p}–${p + step} €`;
        priceSel.appendChild(opt);
      }
    }
    if (current) priceSel.value = current;
  }
};

// 🔄 Alle Filter zurücksetzen und erneut anwenden
window.resetFilter = function () {
  document.querySelectorAll(".filterbar select").forEach((sel) => {
    sel.selectedIndex = 0;
  });
  populateFilterOptions();
  const priceSel = document.getElementById("filter-price");
  if (priceSel) priceSel.selectedIndex = 0;

  const suche = document.getElementById("produktsuche");
  if (suche) {
    suche.value = "";
  }

  const sortSel = document.getElementById("sort-select");
  if (sortSel) {
    sortSel.selectedIndex = 0;
  }
  if (typeof restoreOriginalOrder === "function") {
    restoreOriginalOrder();
  }
  applyFilter();
  if (typeof produktSuche === "function") {
    produktSuche();
  }
  updateActiveFilters();
};

// Sortiert die angezeigten Produkte nach Preis
window.sortProducts = function (order) {
  const container = document.getElementById("produktContainer");
  if (!container) return;

  if (order === "default") {
    restoreOriginalOrder();
  } else {
    const items = Array.from(container.querySelectorAll(".einzelprodukt"));
    items.sort((a, b) => {
      if (order === "price-asc" || order === "price-desc") {
        const pa = parseFloat(a.dataset.preis) || 0;
        const pb = parseFloat(b.dataset.preis) || 0;
        return order === "price-asc" ? pa - pb : pb - pa;
      }
      if (order === "name-asc" || order === "name-desc") {
        const na = a.querySelector("h3")?.textContent.trim() || "";
        const nb = b.querySelector("h3")?.textContent.trim() || "";
        return order === "name-asc"
          ? na.localeCompare(nb)
          : nb.localeCompare(na);
      }
      return 0;
    });
    items.forEach((el) => container.appendChild(el));
  }

  currentPage = 1;
  updatePagination();
  updateActiveFilters();
};

// Setzt die Produkte in ihre ursprüngliche Reihenfolge zurück
window.restoreOriginalOrder = function () {
  const container = document.getElementById("produktContainer");
  if (!container) return;
  const items = Array.from(container.querySelectorAll(".einzelprodukt"));
  items.sort((a, b) => {
    const ia = parseInt(a.dataset.id, 10) || 0;
    const ib = parseInt(b.dataset.id, 10) || 0;
    return ia - ib;
  });
  items.forEach((el) => container.appendChild(el));
};

function updateActiveFilters() {
  document.querySelectorAll(".filterbar select").forEach((sel) => {
    if (sel.id === "sort-select") {
      sel.classList.toggle("active", sel.value !== "default");
    } else {
      sel.classList.toggle("active", sel.selectedIndex > 0);
    }
  });
  const priceSel = document.getElementById("filter-price");
  const priceActive = priceSel && priceSel.selectedIndex > 0;
  if (priceSel) priceSel.classList.toggle("active", priceActive);
}

// Ein/Ausblenden der Filterleiste
window.toggleFilterBar = function () {
  const bar = document.querySelector(".filterbar");
  const btn = document.querySelector(".filter-toggle");
  if (!bar || !btn) return;
  const hidden = bar.classList.toggle("hidden");
  btn.textContent = hidden ? "Filter anzeigen ▼" : "Filter ausblenden ▲";
};

function adjustFilterBar() {
  const bar = document.querySelector(".filterbar");
  const btn = document.querySelector(".filter-toggle");
  if (!bar || !btn) return;
  const mobile = window.innerWidth <= 600;
  bar.classList.toggle("hidden", mobile);
  btn.textContent = mobile ? "Filter anzeigen ▼" : "Filter ausblenden ▲";
}

// Wechselt zwischen Listen- und Grid-Layout für die Produktübersicht
window.toggleLayout = function () {
  const container = document.getElementById("produktContainer");
  if (!container) return;

  const useList = !container.classList.contains("einzelprodukt-list");
  if (useList) {
    container.classList.add("einzelprodukt-list");
    container.classList.remove("einzelprodukt-grid");
  } else {
    container.classList.add("einzelprodukt-grid");
    container.classList.remove("einzelprodukt-list");
  }

  const layout = useList ? "list" : "grid";
  localStorage.setItem("productLayout", layout);
  updateLayoutToggle(layout);

  applyFilter();
};

function updateLayoutToggle(layout) {
  const btn = document.querySelector(".layout-toggle");
  if (!btn) return;
  if (layout === "list") {
    btn.textContent = "🔳 Grid anzeigen";
  } else {
    btn.textContent = "☰ Liste anzeigen";
  }
}

function showPage(page) {
  const itemsPerPage = getItemsPerPage();
  const start = (page - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  currentPage = page;

  paginatedItems.forEach((el, idx) => {
    el.style.display = idx >= start && idx < end ? "" : "none";
  });

  document.querySelectorAll(".pagination button.page").forEach((btn) => {
    btn.classList.toggle("active", parseInt(btn.dataset.page) === currentPage);
  });

  const prev = document.querySelector(".pagination button.prev");
  const next = document.querySelector(".pagination button.next");
  const totalPages = Math.max(
    1,
    Math.ceil(paginatedItems.length / getItemsPerPage())
  );
  if (prev) prev.disabled = currentPage === 1;
  if (next) next.disabled = currentPage === totalPages;
}

function renderPagination(total) {
  const container = document.querySelector(".pagination");
  if (!container) return;
  container.innerHTML = "";

  const prev = document.createElement("button");
  prev.className = "prev";
  prev.innerHTML = "&laquo;";
  container.appendChild(prev);

  for (let i = 1; i <= total; i++) {
    const b = document.createElement("button");
    b.className = `page${i === currentPage ? " active" : ""}`;
    b.dataset.page = i;
    b.textContent = i;
    container.appendChild(b);
  }

  const next = document.createElement("button");
  next.className = "next";
  next.innerHTML = "&raquo;";
  container.appendChild(next);

  prev.addEventListener("click", () => {
    if (currentPage > 1) showPage(currentPage - 1);
  });
  next.addEventListener("click", () => {
    const totalPages = Math.max(
      1,
      Math.ceil(paginatedItems.length / getItemsPerPage())
    );
    if (currentPage < totalPages) showPage(currentPage + 1);
  });
  container.querySelectorAll("button.page").forEach((btn) => {
    btn.addEventListener("click", () => {
      showPage(parseInt(btn.dataset.page));
    });
  });

  showPage(currentPage);
}

function updatePagination() {
  const container = document.getElementById("produktContainer");
  if (!container) return;
  // Nur Elemente berücksichtigen, die nicht durch Filter ausgeblendet wurden
  // (data-visible="1"), unabhängig von der aktuellen Seitennummer
  paginatedItems = Array.from(
    container.querySelectorAll('.einzelprodukt')
  ).filter((el) => el.dataset.visible !== "0");
  const itemsPerPage = getItemsPerPage();
  const totalPages = Math.max(
    1,
    Math.ceil(paginatedItems.length / itemsPerPage)
  );
  if (currentPage > totalPages) currentPage = totalPages;
  renderPagination(totalPages);
}
// Nach dem kompletten Laden der Seite erneut Pagination berechnen,
// damit alle Produkte und Layout-Styles berücksichtigt werden
window.addEventListener("load", () => {
  updatePagination();
});


window.addEventListener("resize", () => {
  updatePagination();
});

