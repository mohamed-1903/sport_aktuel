// ✅ FILTERFUNKTION
window.applyFilter = function () {
  const filterWerte = {
    marke: document.getElementById("filter-marke")?.value || "",
    farbe: document.getElementById("filter-farbe")?.value || "",
    maxPreis:
      parseFloat(document.getElementById("filter-preis")?.value) || Infinity,
    mannschaft: document.getElementById("filter-mannschaft")?.value || "",
    geschlecht: document.getElementById("filter-geschlecht")?.value || "",
  };

  document.querySelectorAll(".einzelprodukt").forEach((produkt) => {
    const p = produkt.dataset;
    const preis = parseFloat(p.preis);
    const sichtbar =
      (!filterWerte.marke || p.marke === filterWerte.marke) &&
      (!filterWerte.farbe || p.farbe === filterWerte.farbe) &&
      (!filterWerte.mannschaft || p.mannschaft === filterWerte.mannschaft) &&
      (!filterWerte.geschlecht || p.geschlecht === filterWerte.geschlecht) &&
      preis <= filterWerte.maxPreis;

    // leeres Display lässt die ursprüngliche Flex-Darstellung erhalten
    produkt.style.display = sichtbar ? "" : "none";
  });
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

document.addEventListener("DOMContentLoaded", () => {
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
          const url = `index.php?page=product&action=search&query=${encodeURIComponent(query)}`;
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

  // Produktdaten aus JSON-Datei laden
  // Pfad relativ zum Projektstamm
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
});

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
    applyFilter();
    produktSuche();
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

// 🔄 Alle Filter zurücksetzen und erneut anwenden
window.resetFilter = function () {
  document.querySelectorAll(".filterbar select").forEach((sel) => {
    sel.selectedIndex = 0;
  });
  const suche = document.getElementById("produktsuche");
  if (suche) {
    suche.value = "";
  }
  applyFilter();
  if (typeof produktSuche === "function") {
    produktSuche();
  }
};

// Sortiert die angezeigten Produkte nach Preis
window.sortProducts = function (order) {
  const container = document.getElementById("produktContainer");
  if (!container) return;
  const items = Array.from(container.querySelectorAll(".einzelprodukt"));

  items.sort((a, b) => {
    const pa = parseFloat(a.dataset.preis) || 0;
    const pb = parseFloat(b.dataset.preis) || 0;
    return order === "asc" ? pa - pb : pb - pa;
  });

  items.forEach((el) => container.appendChild(el));
};

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
