const scrollToTopBtn = document.getElementById("scrollToTopBtn");

function checkScroll() {
  if (window.pageYOffset > 50) {
    scrollToTopBtn.classList.add("show");
  } else {
    scrollToTopBtn.classList.remove("show");
  }
}

window.addEventListener("DOMContentLoaded", checkScroll);

window.addEventListener("scroll", checkScroll);

scrollToTopBtn.addEventListener("click", () => {
  window.scrollTo({
    top: 0,
    behavior: "smooth",
  });
});
