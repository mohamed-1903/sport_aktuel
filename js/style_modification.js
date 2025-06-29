// ✅ Dark-/Light-Mode Toggle
// IIFE that manages theme selection and persists the choice
(() => {
  // Load previously selected theme or default to dark mode
  const savedTheme = localStorage.getItem("theme") || "dark";

  // Apply the given theme classes to the document
  const applyTheme = (theme) => {
    document.body.classList.toggle("light-mode", theme === "light");
    document.body.classList.toggle("dark-mode", theme === "dark");
    document.body.setAttribute("data-theme", theme);
  };

  applyTheme(savedTheme);

  // Initialize the toggle button and handle clicks
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

  // Ensure toggle initialization after DOM is loaded
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initToggle);
  } else {
    initToggle();
  }
})();

// ✅ Scroll-To-Top Button
// Adds a button that smoothly scrolls back to the page top
document.addEventListener("DOMContentLoaded", () => {
  const scrollBtn = document.getElementById("scrollTopBtn");
  if (!scrollBtn) return;

  // Toggle button visibility depending on scroll position
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
