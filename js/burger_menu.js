document.addEventListener('DOMContentLoaded', () => {
  const burger = document.getElementById('burger-menu');
  const nav = document.querySelector('nav');
  const overlay = document.getElementById('nav-overlay');
  if (!burger || !nav || !overlay) return;

  const toggleMenu = () => {
    nav.classList.toggle('open');
    document.body.classList.toggle('nav-open');
  };

  burger.addEventListener('click', toggleMenu);
  overlay.addEventListener('click', toggleMenu);
});
