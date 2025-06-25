// Simple cookie consent and check

document.addEventListener('DOMContentLoaded', () => {
  if (!navigator.cookieEnabled) {
    alert('Bitte aktivieren Sie Cookies, um diese Seite nutzen zu können.');
    return;
  }

  const banner = document.getElementById('cookie-banner');
  const acceptBtn = document.getElementById('cookie-accept');
  const declineBtn = document.getElementById('cookie-decline');
  const settingsBtn = document.getElementById('cookie-settings');
  if (!banner) return;

  const getConsent = () => {
    const match = document.cookie
      .split('; ')
      .find(c => c.startsWith('cookieConsent='));
    return match ? match.split('=')[1] : null;
  };

  const showBanner = () => {
    banner.style.display = 'flex';
  };

  const hideBanner = () => {
    banner.style.display = 'none';
  };

  if (!getConsent()) {
    showBanner();
  }

  acceptBtn?.addEventListener('click', () => {
    const d = new Date();
    d.setFullYear(d.getFullYear() + 1);
    document.cookie = 'cookieConsent=1; expires=' + d.toUTCString() + '; path=/';
    hideBanner();
  });

  declineBtn?.addEventListener('click', () => {
    const d = new Date();
    d.setFullYear(d.getFullYear() + 1);
    document.cookie = 'cookieConsent=0; expires=' + d.toUTCString() + '; path=/';
    hideBanner();
  });

  settingsBtn?.addEventListener('click', showBanner);
});
