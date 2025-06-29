//Mohamed


// Wartet, bis der komplette DOM geladen ist
document.addEventListener('DOMContentLoaded', () => {
  
  // Sucht nach dem Element mit der Klasse 'dropdown-konto'
  const dropdown = document.querySelector('.dropdown-konto');
  if (!dropdown) return; // Falls es nicht existiert, wird der Code abgebrochen

  // Sucht innerhalb des Dropdowns nach dem Button und dem Popup
  const button = dropdown.querySelector('button');
  const popup = dropdown.querySelector('.konto-popup');
  if (!button || !popup) return; // Falls eines von beiden fehlt, wird nichts weiter gemacht

  // Funktion zum Schließen des Popups
  const closePopup = () => {
    popup.classList.remove('show'); // Blendet das Popup aus
    button.setAttribute('aria-expanded', 'false'); // Setzt den ARIA-Zustand auf "nicht geöffnet"
  };

  // Klick auf den Button öffnet oder schließt das Popup
  button.addEventListener('click', (e) => {
    e.stopPropagation(); // Verhindert, dass der Klick nach oben weitergegeben wird
    const isOpen = popup.classList.toggle('show'); // Toggle: zeigt oder versteckt das Popup
    button.setAttribute('aria-expanded', isOpen ? 'true' : 'false'); // Setzt den Zustand je nach Sichtbarkeit
  });

  // Klick außerhalb des Dropdowns schließt das Popup
  document.addEventListener('click', (e) => {
    if (!dropdown.contains(e.target)) { // Prüft, ob der Klick außerhalb liegt
      closePopup(); // Falls ja, wird das Popup geschlossen
    }
  });

});
