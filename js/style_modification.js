// ✅ Dark-/Light-Mode Toggle
document.addEventListener("DOMContentLoaded", () => {
  const toggleButton = document.getElementById("theme-toggle");
  if (!toggleButton) return;

  const applyTheme = (theme) => {
    document.body.classList.toggle("light-mode", theme === "light");
    document.body.classList.toggle("dark-mode", theme === "dark");
    document.body.setAttribute("data-theme", theme);
    toggleButton.textContent = theme === "light" ? "🌙" : "☀️";
  };

  fetch("index.php?page=theme&action=get")
    .then((r) => r.json())
    .then((d) => applyTheme(d.theme || "dark"));

  toggleButton.addEventListener("click", () => {
});
      .then((r) => r.json())
      .then((d) => applyTheme(d.theme));
  });
});

// ✅ Scroll-To-Top Button
document.addEventListener("DOMContentLoaded", () => {
  const scrollBtn = document.getElementById("scrollTopBtn");
  if (!scrollBtn) return;

  const checkScrollAvailability = () => {
    const scrollable =
      document.documentElement.scrollHeight > window.innerHeight;
    scrollBtn.style.display =
      scrollable && window.scrollY > 300 ? "flex" : "none";
  };

  window.addEventListener("scroll", checkScrollAvailability);
  window.addEventListener("resize", checkScrollAvailability);
  checkScrollAvailability();

  scrollBtn.addEventListener("click", () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
  });
});