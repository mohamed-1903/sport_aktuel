// js/darkmode.js

document.addEventListener("DOMContentLoaded", () => {
  const toggleBtn = document.getElementById("theme-toggle");
  const body = document.body;

  // Zustand aus localStorage wiederherstellen␊
  const savedMode = localStorage.getItem("theme-mode");
  if (savedMode === "light") {
    body.classList.add("light-mode");
  }

  // Toggle-Logik␊
  toggleBtn?.addEventListener("click", () => {
    body.classList.toggle("light-mode");
    const mode = body.classList.contains("light-mode") ? "light" : "dark";
    localStorage.setItem("theme-mode", mode);
  });
});

// Scroll-To-Top Button
document.addEventListener("DOMContentLoaded", () => {
    const scrollBtn = document.getElementById("scrollTopBtn");
    if (!scrollBtn) return;

    const checkScrollAvailability = () => {
        const scrollable = document.documentElement.scrollHeight > window.innerHeight;
        scrollBtn.style.display = scrollable && window.scrollY > 300 ? "flex" : "none";
    };

    window.addEventListener("scroll", checkScrollAvailability);
    window.addEventListener("resize", checkScrollAvailability);
    checkScrollAvailability();

    scrollBtn.addEventListener("click", () => {
        window.scrollTo({ top: 0, behavior: "smooth" });
    });
});

// Body Transition Effect
document.addEventListener("DOMContentLoaded", () => {
    document.body.classList.add("transitioning");
    setTimeout(() => {
        document.body.classList.remove("transitioning");
    }, 400);
});
