//Laith

// Führt den Code aus, sobald der DOM vollständig geladen ist
document.addEventListener("DOMContentLoaded", () => {

  // Prüft, ob Cookies im Browser aktiviert sind
  if (!navigator.cookieEnabled) {
    alert("Bitte aktivieren Sie Cookies, um diese Seite nutzen zu können.");
    return; // Bricht ab, wenn Cookies deaktiviert sind
  }

  // Elemente für das Cookie-Banner abrufen
  const banner = document.getElementById("cookie-banner");        // Der Banner selbst
  const acceptBtn = document.getElementById("cookie-accept");     // Button "Akzeptieren"
  const declineBtn = document.getElementById("cookie-decline");   // Button "Ablehnen"
  const settingsBtn = document.getElementById("cookie-settings"); // Button "Einstellungen"
  if (!banner) return; // Wenn kein Banner gefunden wird, abbrechen

  // Hilfsfunktion: Liest die bereits gegebene Zustimmung aus dem Cookie
  const getConsent = () => {
    const match = document.cookie
      .split("; ")
      .find((c) => c.startsWith("cookieConsent=")); // Suche nach dem entsprechenden Cookie
    return match ? match.split("=")[1] : null;       // Gibt '1' oder '0' zurück oder null
  };

  // Banner anzeigen
  const showBanner = () => {
    banner.style.display = "flex";
  };

  // Banner ausblenden
  const hideBanner = () => {
    banner.style.display = "none";
  };

  // Zeige Banner nur, wenn noch keine Zustimmung gespeichert wurde
  if (!getConsent()) {
    showBanner();
  }

  // Zustimmung: Cookie mit Wert "1" setzen und Banner ausblenden
  acceptBtn?.addEventListener("click", () => {
    const d = new Date();
    d.setFullYear(d.getFullYear() + 1); // Cookie ein Jahr gültig
    document.cookie =
      "cookieConsent=1; expires=" + d.toUTCString() + "; path=/";
    hideBanner();
  });

  // Ablehnung: Cookie mit Wert "0" setzen und Banner ausblenden
  declineBtn?.addEventListener("click", () => {
    const d = new Date();
    d.setFullYear(d.getFullYear() + 1);
    document.cookie =
      "cookieConsent=0; expires=" + d.toUTCString() + "; path=/";
    hideBanner();
  });

  // "Einstellungen"-Button zeigt erneut den Banner
  settingsBtn?.addEventListener("click", showBanner);
});
