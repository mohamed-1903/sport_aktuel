//Mohamed

// Führt den Code erst aus, wenn das komplette DOM geladen ist
document.addEventListener('DOMContentLoaded', () => {

  // Elemente aus dem DOM abrufen
  const burger = document.getElementById('burger-menu');     // Burger-Icon für Menü
  const nav = document.querySelector('nav');                 // Navigationselement
  const overlay = document.getElementById('nav-overlay');    // Overlay-Hintergrund
  if (!burger || !nav || !overlay) return;                   // Abbrechen, falls etwas fehlt

  // Funktion zum Öffnen/Schließen des Menüs
  const toggleMenu = () => {
    nav.classList.toggle('open');                            // Schaltet Klasse für sichtbare Navigation
    document.body.classList.toggle('nav-open');              // Klasse am Body für evtl. Scrolling/Overlay
  };

  // Menü öffnen/schließen beim Klick auf Burger-Icon oder Overlay
  burger.addEventListener('click', toggleMenu);
  overlay.addEventListener('click', toggleMenu);

  // Dropdown-Links im mobilen Menü (nur bis 768px aktiv)
  const dropdownLinks = nav.querySelectorAll('.dropdown > a');
  dropdownLinks.forEach((link) => {
    link.addEventListener('click', (e) => {
      if (window.innerWidth <= 768) {

        const item = link.parentElement;                      // .dropdown-Element
        const isOpen = item.classList.contains('open');       // Hat es bereits die Klasse 'open'?

        if (!isOpen) {
          e.preventDefault();                                 // Verhindert das direkte Springen des Links
          // Schließt alle anderen Dropdowns
          nav.querySelectorAll('.dropdown').forEach((el) => el.classList.remove('open'));
          item.classList.add('open');                         // Öffnet das angeklickte Dropdown
        } else {
          toggleMenu();                                       // Falls bereits offen, wird das Menü komplett geschlossen
        }
      }
    });
  });

  // Wenn ein Untermenü-Link geklickt wird, schließt das Menü (nur bei mobiler Ansicht)
  const submenuLinks = nav.querySelectorAll('.dropdown-menu a');
  submenuLinks.forEach((lnk) => {
    lnk.addEventListener('click', () => {
      if (window.innerWidth <= 768) {
        toggleMenu();                                         // Menü schließen
      }
    });
  });

  // Wenn die Fenstergröße über 768px geht, wird das Menü automatisch geschlossen
  window.addEventListener('resize', () => {
    if (window.innerWidth > 768 && nav.classList.contains('open')) {
      nav.classList.remove('open');
      document.body.classList.remove('nav-open');
    }
  });
});
