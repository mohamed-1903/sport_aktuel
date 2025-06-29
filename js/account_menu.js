document.addEventListener('DOMContentLoaded', () => {
  const dropdown = document.querySelector('.dropdown-konto');
  if (!dropdown) return;

  const button = dropdown.querySelector('button');
  const popup = dropdown.querySelector('.konto-popup');
  if (!button || !popup) return;

  const closePopup = () => {
    popup.classList.remove('show');
    button.setAttribute('aria-expanded', 'false');
  };

  button.addEventListener('click', (e) => {
    e.stopPropagation();
    const isOpen = popup.classList.toggle('show');
    button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
  });

  document.addEventListener('click', (e) => {
    if (!dropdown.contains(e.target)) {
      closePopup();
    }
  });
});
