const moreBtn = document.getElementById("more-button");
const moreMenu = document.getElementById("more-menu");

moreBtn.addEventListener("click", () => {
  moreMenu.classList.toggle("hidden");
});

document.addEventListener("click", function (e) {
  if (!moreBtn.contains(e.target) && !moreMenu.contains(e.target)) {
    moreMenu.classList.add("hidden");
  }
});

document.addEventListener("DOMContentLoaded", () => {
  const header = document.querySelector("header");
  const sidebar = document.querySelector(".sidebar");
  const h2_first = document.getElementById("h2-first");

  let observer = null;

  const observe = (element) => {
    if (!element) return;

    if (observer) {
      observer.disconnect();
    }

    observer = new IntersectionObserver(([entry]) => {
      sidebar.classList.toggle("header-hidden", !entry.isIntersecting);
    });

    observer.observe(element);
  };

  const updateObserver = () => {
    const width = window.innerWidth;

    if (width <= 430) {
      observe(header);
    } else if (width > 430 && width <= 1340) {
      observe(h2_first);
    } else {
      if (observer) {
        observer.disconnect();
        observer = null;
      }
      sidebar.classList.remove("header-hidden");
    }
  };

  updateObserver();
  window.addEventListener("resize", updateObserver);
});
