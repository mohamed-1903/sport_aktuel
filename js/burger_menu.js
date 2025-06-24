document.addEventListener('DOMContentLoaded', () => {
  const burger = document.getElementById('burger-menu');
  const nav = document.querySelector('nav');
  if (!burger || !nav) return;

  burger.addEventListener('click', () => {
    nav.classList.toggle('open');
  });

  nav.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => {
      nav.classList.remove('open');
    });
  });
});
