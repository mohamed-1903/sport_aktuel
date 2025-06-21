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

    produkt.style.display = sichtbar ? "block" : "none";
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
window.resetFilter = function () {
  document
    .querySelectorAll(".filterbar select")
    .forEach((sel) => (sel.selectedIndex = 0));
  const suche = document.getElementById("produktsuche");
  if (suche) suche.value = "";
  applyFilter();
  produktSuche();
};

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

    input.addEventListener("keydown", (e) =>
      handleTastaturNavigation(e, liste, input, shadow)
    );
  }

  document.addEventListener("click", (e) => {
    if (!e.target.closest(".search-container")) {
      liste.style.display = "none";
      shadow.value = "";
    }
  });

  fetch("produkte.json")
    .then((res) => res.json())
    .then((data) => {
      alleProdukte = data.products || [];
    });

  if (typeof produktKonfigurationen !== "undefined") {
    produktKonfigurationen.forEach(({ containerId, urls }) => {
      ladeProdukte(containerId, urls);
    });
  }
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

  const produkte = Array.from(document.querySelectorAll(".einzelprodukt"));
  const treffer = produkte.filter((el) => {
    const name = el.querySelector("h3")?.innerText.toLowerCase() || "";
    return name.includes(wert);
  });

  if (treffer.length > 0) {
    const match = treffer.find((el) =>
      el.querySelector("h3")?.innerText.toLowerCase().startsWith(wert)
    );
    shadow.value = match?.querySelector("h3")?.innerText || "";

    treffer.slice(0, 5).forEach((el) => {
      const name = el.querySelector("h3")?.innerText;
      const price = parseFloat(el.dataset.preis)?.toFixed(2) + " €" || "Preis?";
      const img = el.querySelector("img")?.src || "";

      const li = document.createElement("li");
      li.innerHTML = `
        <img src="${img}" alt="${name}" />
        <div>
          <strong>${name}</strong><br>
          <small>${price}</small>
        </div>
      `;
      li.addEventListener("click", () => {
        const link = el.querySelector("a")?.href;
        if (link) window.location.href = link;
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
window.resetFilter = function () {
  document
    .querySelectorAll(".filterbar select")
    .forEach((sel) => (sel.selectedIndex = 0));
  const suche = document.getElementById("produktsuche");
  if (suche) suche.value = "";
  applyFilter();
  produktSuche();
};
