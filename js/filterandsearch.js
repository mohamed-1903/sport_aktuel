// js/filterandsearch.js

function applyFilter() {
  const marke = document.getElementById("filter-marke").value.toLowerCase();
  const farbe = document.getElementById("filter-farbe").value.toLowerCase();
  const preis = parseFloat(document.getElementById("filter-preis").value);
  const mannschaft = document.getElementById("filter-mannschaft").value.toLowerCase();
  const geschlecht = document.getElementById("filter-geschlecht").value.toLowerCase();

  const alleProdukte = document.querySelectorAll(".einzelprodukt");

  alleProdukte.forEach((produkt) => {
    const matchMarke = !marke || produkt.dataset.marke.toLowerCase() === marke;
    const matchFarbe = !farbe || produkt.dataset.farbe.toLowerCase() === farbe;
    const matchPreis = !preis || parseFloat(produkt.dataset.preis) <= preis;
    const matchMannschaft = !mannschaft || produkt.dataset.mannschaft.toLowerCase() === mannschaft;
    const matchGeschlecht = !geschlecht || produkt.dataset.geschlecht.toLowerCase() === geschlecht;

    const visible = matchMarke && matchFarbe && matchPreis && matchMannschaft && matchGeschlecht;
    produkt.style.display = visible ? "block" : "none";
  });
}

document.addEventListener("DOMContentLoaded", () => {
  applyFilter();
});

function resetFilter() {
  document.querySelectorAll(
    "#filter-marke, #filter-farbe, #filter-preis, #filter-mannschaft, #filter-geschlecht"
  ).forEach((sel) => (sel.value = ""));
  applyFilter();
}
