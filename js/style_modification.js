// ✅ Dark-/Light-Mode Toggle
(() => {
  const savedTheme = localStorage.getItem("theme") || "dark";

  const applyTheme = (theme) => {
    document.body.classList.toggle("light-mode", theme === "light");
    document.body.classList.toggle("dark-mode", theme === "dark");
    document.body.setAttribute("data-theme", theme);
  };

  applyTheme(savedTheme);

  const initToggle = () => {
    const toggleButton = document.getElementById("theme-toggle");
    if (!toggleButton) return;
    toggleButton.textContent = savedTheme === "light" ? "🌙" : "☀️";
    toggleButton.addEventListener("click", () => {
      const newTheme = document.body.classList.contains("light-mode")
        ? "dark"
        : "light";
      applyTheme(newTheme);
      localStorage.setItem("theme", newTheme);
      toggleButton.textContent = newTheme === "light" ? "🌙" : "☀️";
    });
  };

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initToggle);
  } else {
    initToggle();
  }
})();

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
