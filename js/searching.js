document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("search-username");
  const searchInputIcon = document.querySelector("#search-name-icon");
  const searchInputIconImg = searchInputIcon.querySelector("img");
  const userElements = document.querySelectorAll(".right-users .user-href");
  const rightUsers = document.querySelector(".right-users");

  searchInputIconImg.addEventListener("click", (e) => {
    e.stopPropagation();
    searchInputIcon.innerHTML = ""; // usuń ikonę
    searchInputIcon.dataset.toggled = "true";

    if (rightUsers) {
      rightUsers.style.marginTop = "51px";
    }

    // Utwórz pole input
    const input = document.createElement("input");
    input.type = "text";
    input.placeholder = "Wyszukaj..";
    input.style.width = "90vw";
    input.style.height = "55px";
    input.style.fontSize = "1.1rem";
    input.style.borderRadius = "999px";
    input.style.boxSizing = "border-box";
    input.style.textAlign = "center";
    input.style.border = "2px solid #afb2bc";
    input.style.backgroundColor = "#141414";
    input.style.color = "white";

    // Ustaw kontener
    Object.assign(searchInputIcon.style, {
      position: "fixed",
      width: "calc(100vw - 40px)",
      top: "20px",
      left: "20px",
      right: "20px",
      display: "flex",
      justifyContent: "center",
      alignItems: "center",
      height: "55px",
      zIndex: "1000",
      border: "none",
    });

    searchInputIcon.appendChild(input);
    input.focus();

    // Filtrowanie dla mobilnych
    input.addEventListener("input", () => {
      const query = input.value.trim().toLowerCase();
      userElements.forEach((user) => {
        const usernameEl = user.querySelector(".post-username");
        const username = usernameEl
          ? usernameEl.textContent.trim().toLowerCase()
          : "";
        user.style.display = username.includes(query) ? "flex" : "none";
      });
    });
  });

  // Filtrowanie dla wiekszych ekranow
  searchInput.addEventListener("input", () => {
    const query = searchInput.value.trim().toLowerCase();

    userElements.forEach((user) => {
      const username = user.innerText.toLowerCase();
      if (username.includes(query)) {
        user.style.display = "flex"; // lub 'block' w zależności od stylu
      } else {
        user.style.display = "none";
      }
    });
  });

  document.addEventListener("click", (e) => {
    if (
      !searchInputIcon.contains(e.target) &&
      searchInputIcon.dataset.toggled === "true"
    ) {
      // Przywróć ikonę
      searchInputIcon.innerHTML = "";
      searchInputIcon.appendChild(searchInputIconImg);
      searchInputIconImg.style.display = "block";
      searchInputIcon.style = "";
      delete searchInputIcon.dataset.toggled;
      if (rightUsers) {
        rightUsers.style.marginTop = "0px";
      }

      if (searchInput) {
        searchInput.value = "";
      }
      const dynamicInput = searchInputIcon.querySelector("input");
      if (dynamicInput) {
        dynamicInput.value = "";
      }

      userElements.forEach((user) => {
        user.style.display = "flex";
      });
    }
    if (rightUsers) {
      rightUsers.style.marginTop = "0px"; // reset marginesu
    }
  });
}); // <-- zamknięcie DOMContentLoaded
