// ✅ Dark-/Light-Mode Toggle
document.addEventListener("DOMContentLoaded", () => {
  const toggleButton = document.getElementById("theme-toggle");
  const savedTheme = localStorage.getItem("theme") || "dark";

  if (savedTheme === "light") {
    document.body.classList.add("light-mode");
    document.body.setAttribute("data-theme", "light");
    toggleButton.textContent = "🌙";
  } else {
    document.body.classList.add("dark-mode");
    document.body.setAttribute("data-theme", "dark");
    toggleButton.textContent = "☀️";
  }

  toggleButton.addEventListener("click", () => {
    document.body.classList.toggle("light-mode");
    document.body.classList.toggle("dark-mode");
    const theme = document.body.classList.contains("light-mode")
      ? "light"
      : "dark";
    document.body.setAttribute("data-theme", theme);
    localStorage.setItem("theme", theme);
    toggleButton.textContent = theme === "light" ? "🌙" : "☀️";
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