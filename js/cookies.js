// Simple cookie consent and check

document.addEventListener('DOMContentLoaded', () => {
  // Warn if cookies are disabled
  if (!navigator.cookieEnabled) {
    alert('Bitte aktivieren Sie Cookies, um diese Seite nutzen zu können.');
    return;
  }

  const banner = document.getElementById('cookie-banner');
  const acceptBtn = document.getElementById('cookie-accept');

  if (!banner || !acceptBtn) return;

  const hasConsent = document.cookie.split('; ').some(c => c.startsWith('cookieConsent='));
  if (!hasConsent) {
    banner.style.display = 'flex';
  }

  acceptBtn.addEventListener('click', () => {
    const d = new Date();
    d.setFullYear(d.getFullYear() + 1);
    document.cookie = 'cookieConsent=1; expires=' + d.toUTCString() + '; path=/';
    banner.style.display = 'none';
  });
});
