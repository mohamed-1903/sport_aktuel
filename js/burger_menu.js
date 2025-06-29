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

  const dropdownLinks = nav.querySelectorAll('.dropdown > a');
  dropdownLinks.forEach((link) => {
    link.addEventListener('click', (e) => {
      if (window.innerWidth <= 992) {
        e.preventDefault();
        const item = link.parentElement;
        const isOpen = item.classList.contains('open');
        nav.querySelectorAll('.dropdown').forEach((el) => el.classList.remove('open'));
        if (!isOpen) item.classList.add('open');
      }
    });
  });

  // Close menu when window is resized above mobile breakpoint
  window.addEventListener('resize', () => {
    if (window.innerWidth > 992 && nav.classList.contains('open')) {
      nav.classList.remove('open');
      document.body.classList.remove('nav-open');
    }
  });
});