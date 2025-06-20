
// Filterfunktion
window.applyFilter = function () {
  // Liest die aktuellen Werte der Filterfelder aus
  const filterWerte = {
    marke: document.getElementById("filter-marke")?.value || "", // Marke
    farbe: document.getElementById("filter-farbe")?.value || "", // Farbe
    maxPreis:
      parseFloat(document.getElementById("filter-preis")?.value) || Infinity, // Maximaler Preis
    mannschaft: document.getElementById("filter-mannschaft")?.value || "", // Mannschaft
    geschlecht: document.getElementById("filter-geschlecht")?.value || "", // Geschlecht
  };

  // Holt alle Produkte mit der Klasse "einzelprodukt"
  const produkte = document.querySelectorAll(".einzelprodukt");

  // Für jedes Produkt wird geprüft, ob es die Filter erfüllt
  produkte.forEach((produkt) => {
    const pMarke = produkt.getAttribute("data-marke"); // Marke des Produkts
    const pFarbe = produkt.getAttribute("data-farbe"); // Farbe
    const pPreis = parseFloat(produkt.getAttribute("data-preis")); // Preis
    const pMannschaft = produkt.getAttribute("data-mannschaft"); // Mannschaft
    const pGeschlecht = produkt.getAttribute("data-geschlecht"); // Geschlecht

    // Überprüft, ob alle Filterbedingungen erfüllt sind
    const filterErfüllt =
      (filterWerte.marke === "" || pMarke === filterWerte.marke) &&
      (filterWerte.farbe === "" || pFarbe === filterWerte.farbe) &&
      (filterWerte.maxPreis === Infinity || pPreis <= filterWerte.maxPreis) &&
      (filterWerte.mannschaft === "" ||
        pMannschaft === filterWerte.mannschaft) &&
      (filterWerte.geschlecht === "" || pGeschlecht === filterWerte.geschlecht);

    // Zeigt oder versteckt das Produkt je nach Filterergebnis
    produkt.style.display = filterErfüllt ? "block" : "none";
  });
};
// Funktion zur Produktsuche über ein Eingabefeld
function produktSuche() {
  const eingabe = document
    .getElementById("produktsuche")
    ?.value.toLowerCase()
    .trim();
  const produkte = document.querySelectorAll(".einzelprodukt");
  const feedbackEl = document.getElementById("such-feedback");

  if (!eingabe || eingabe.length < 2) {
    // Wenn zu kurz: alles zeigen
    produkte.forEach((p) => (p.style.display = "block"));
    if (feedbackEl) feedbackEl.textContent = "";
    return;
  }

  let treffer = 0;

  produkte.forEach((produkt) => {
    const name = produkt.querySelector("h3")?.innerText.toLowerCase() || "";
    const marke = produkt.dataset.marke?.toLowerCase() || "";
    const farbe = produkt.dataset.farbe?.toLowerCase() || "";
    const geschlecht = produkt.dataset.geschlecht?.toLowerCase() || "";
    const mannschaft = produkt.dataset.mannschaft?.toLowerCase() || "";

    const text = `${name} ${marke} ${farbe} ${geschlecht} ${mannschaft}`;
    const sichtbar = text.includes(eingabe);

    produkt.style.display = sichtbar ? "block" : "none";
    if (sichtbar) treffer++;
  });

  if (feedbackEl) {
    feedbackEl.textContent =
      treffer === 0
        ? "❌ Keine passenden Produkte gefunden."
        : `✅ ${treffer} Produkt${treffer === 1 ? "" : "e"} gefunden.`;
  }
}
// ✅ Live-Suche mit Autocomplete + Vorschau
let alleProdukte = [];
let autocompleteVorschlag = "";
let fokusIndex = -1;

document.addEventListener("DOMContentLoaded", () => {
  const input = document.getElementById("produktsuche");
  const shadow = document.getElementById("autocomplete-shadow");
  const liste = document.getElementById("such-vorschlaege");
  const feedbackEl = document.getElementById("such-feedback");

  // 🔁 Input Events
  if (input) {
    input.addEventListener("input", () => {
      produktSuche();
      autocompleteSuche();
    });

    input.addEventListener("keydown", (e) =>
      handleTastaturNavigation(e, liste, input, shadow)
    );
  }

  // ❌ Liste schließen bei Klick außerhalb
  document.addEventListener("click", (e) => {
    if (!e.target.closest(".search-container")) {
      liste.style.display = "none";
      shadow.value = "";
    }
  });

  // 📦 Produkte für Autocomplete laden
  fetch("produkte.json")
    .then((res) => res.json())
    .then((data) => {
      alleProdukte = data.products || [];
    });

  // 🔄 Dynamisches Laden der Produktcontainer
  if (typeof produktKonfigurationen !== "undefined") {
    produktKonfigurationen.forEach(({ containerId, urls }) => {
      ladeProdukte(containerId, urls);
    });
  }
});

// 🔽 PRODUKT LADEN
function ladeProdukte(containerId, urls) {
  const container = document.getElementById(containerId);
  if (!container) return console.warn("⚠️ Container fehlt:", containerId);

  const fetchPromises = urls.map((url) =>
    fetch(url)
      .then((res) => res.text())
      .then((html) => {
        const temp = document.createElement("div");
        temp.innerHTML = html;
        Array.from(temp.children).forEach((el) =>
          container.appendChild(el)
        );
      })
  );

  Promise.all(fetchPromises).then(() => applyFilter());
}

// 🔍 TEXT-SUCHE
function produktSuche() {
  const eingabe = document.getElementById("produktsuche")?.value.toLowerCase().trim();
  const produkte = document.querySelectorAll(".einzelprodukt");
  const feedbackEl = document.getElementById("such-feedback");

  if (!eingabe || eingabe.length < 2) {
    produkte.forEach((p) => (p.style.display = "block"));
    if (feedbackEl) feedbackEl.textContent = "";
    return;
  }

  let treffer = 0;

  produkte.forEach((produkt) => {
    const text = [
      produkt.querySelector("h3")?.innerText,
      produkt.dataset.marke,
      produkt.dataset.farbe,
      produkt.dataset.geschlecht,
      produkt.dataset.mannschaft,
    ]
      .map((s) => (s || "").toLowerCase())
      .join(" ");

    const sichtbar = text.includes(eingabe);
    produkt.style.display = sichtbar ? "block" : "none";
    if (sichtbar) treffer++;
  });

  if (feedbackEl) {
    feedbackEl.textContent =
      treffer === 0
        ? "❌ Keine passenden Produkte gefunden."
        : `✅ ${treffer} Produkt${treffer === 1 ? "" : "e"} gefunden.`;
  }
}

// ✨ AUTOCOMPLETE
function autocompleteSuche() {
  const input = document.getElementById("produktsuche");
  const liste = document.getElementById("such-vorschlaege");
  const shadow = document.getElementById("autocomplete-shadow");

  const wert = input.value.toLowerCase().trim();
  liste.innerHTML = "";
  autocompleteVorschlag = "";
  fokusIndex = -1;

  const match = alleProdukte.find((p) =>
    p.name.toLowerCase().startsWith(wert)
  );
  shadow.value = wert && match ? match.name : "";

  const treffer = alleProdukte.filter((p) =>
    p.name.toLowerCase().includes(wert)
  );

  if (treffer.length > 0) {
    treffer.slice(0, 5).forEach((p) => {
      const li = document.createElement("li");
      li.innerHTML = `
        <img src="${p.imageMain}" alt="${p.name}" />
        <div>
          <strong>${p.name}</strong><br>
          <small>${p.priceValue.toFixed(2)} €</small>
        </div>`;
      li.addEventListener("click", () => {
        window.location.href = `Produkt_Sport.php?iid=${p.iid}`;
      });
      liste.appendChild(li);
    });
    liste.style.display = "block";
  } else {
    liste.innerHTML = `<li class="keine-treffer-box"><div class="keine-treffer-icon">🔍</div><div><strong>Keine Treffer</strong></div></li>`;
    liste.style.display = "block";
  }
}

// ⌨️ TASTATUR-NAVIGATION
function handleTastaturNavigation(e, liste, input, shadow) {
  const eintraege = liste.querySelectorAll("li");
  if (!eintraege.length) return;

  if (e.key === "ArrowDown") {
    e.preventDefault();
    fokusIndex = (fokusIndex + 1) % eintraege.length;
    updateFokus(eintraege);
  } else if (e.key === "ArrowUp") {
    e.preventDefault();
    fokusIndex = (fokusIndex - 1 + eintraege.length) % eintraege.length;
    updateFokus(eintraege);
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
}

function updateFokus(eintraege) {
  eintraege.forEach((li, i) => {
    li.classList.toggle("focused", i === fokusIndex);
    if (i === fokusIndex) li.scrollIntoView({ block: "nearest" });
  });
}

// 🧪 FILTER
window.applyFilter = function () {
  const filterWerte = {
    marke: document.getElementById("filter-marke")?.value || "",
    farbe: document.getElementById("filter-farbe")?.value || "",
    maxPreis: parseFloat(document.getElementById("filter-preis")?.value) || Infinity,
    mannschaft: document.getElementById("filter-mannschaft")?.value || "",
    geschlecht: document.getElementById("filter-geschlecht")?.value || "",
  };

  document.querySelectorAll(".einzelprodukt").forEach((produkt) => {
    const pMarke = produkt.dataset.marke;
    const pFarbe = produkt.dataset.farbe;
    const pPreis = parseFloat(produkt.dataset.preis);
    const pMannschaft = produkt.dataset.mannschaft;
    const pGeschlecht = produkt.dataset.geschlecht;

    const passt =
      (filterWerte.marke === "" || pMarke === filterWerte.marke) &&
      (filterWerte.farbe === "" || pFarbe === filterWerte.farbe) &&
      (filterWerte.maxPreis === Infinity || pPreis <= filterWerte.maxPreis) &&
      (filterWerte.mannschaft === "" || pMannschaft === filterWerte.mannschaft) &&
      (filterWerte.geschlecht === "" || pGeschlecht === filterWerte.geschlecht);

    produkt.style.display = passt ? "block" : "none";
  });
};
