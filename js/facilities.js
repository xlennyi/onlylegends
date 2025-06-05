const accessibilityBtn = document.createElement("img");

function updateAccessibilityButtonSize() {
  if (window.innerWidth <= 430) {
    accessibilityBtn.style.width = "36px";
    accessibilityBtn.style.height = "36px";
    accessibilityBtn.style.padding = "7px";
    accessibilityBtn.style.boxSizing = "border-box";
  } else {
    accessibilityBtn.style.width = "55px";
    accessibilityBtn.style.height = "55px";
    accessibilityBtn.style.padding = "11px";
    accessibilityBtn.style.boxSizing = "border-box";
  }
}

accessibilityBtn.src = "images/disabled.png";
Object.assign(accessibilityBtn.style, {
  position: "fixed",
  left: "13px",
  bottom: "20px",
  borderRadius: "50%",
  cursor: "pointer",
  zIndex: "9999",
  objectFit: "cover",
  backgroundColor: "#0024F5",
  border: "1px solid white",
  transition: "all 0.3s ease",
});

const menu = document.createElement("div");
menu.id = "accessibility-menu";

Object.assign(menu.style, {
  position: "fixed",
  left: "80px",
  bottom: "20px",
  backgroundColor: "#fff",
  border: "1px solid #ccc",
  borderRadius: "12px",
  boxShadow: "0 4px 8px rgba(0,0,0,0.1)",
  padding: "12px",
  display: "none",
  zIndex: "9999",
});

function increaseFontSize(current) {
  const size = parseInt(current.replace("px", ""));
  return size + 2 + "px";
}

function decreaseFontSize(current) {
  const size = parseInt(current.replace("px", ""));
  return size - 2 + "px";
}

const zoomIn = document.createElement("button");
zoomIn.textContent = "🔍 Powiększ tekst";
zoomIn.onclick = () => {
  document.body.style.fontSize = increaseFontSize(
    document.body.style.fontSize || "16px"
  );
};

const zoomOut = document.createElement("button");
zoomOut.textContent = "🔽 Zmniejsz tekst";
zoomOut.onclick = () => {
  document.body.style.fontSize = decreaseFontSize(
    document.body.style.fontSize || "16px"
  );
};

const webColorBtn = document.createElement("button");
webColorBtn.textContent = "🎨 Zmień kolor strony";
document.body.appendChild(webColorBtn); // dodajemy przycisk do strony

let colorMode = 0;

webColorBtn.onclick = () => {
  let bgColor,
    textColor,
    btnBgColor,
    btnTextColor,
    placeHolderColor,
    placeHolderBgColor;

  if (colorMode === 0) {
    bgColor = "black";
    textColor = "white";
    btnBgColor = "white";
    btnTextColor = "black";
    placeHolderColor = "placeholder-black";
    placeHolderBgColor = "white";
  } else if (colorMode === 1) {
    bgColor = "black";
    textColor = "yellow";
    btnBgColor = "yellow";
    btnTextColor = "black";
    placeHolderColor = "placeholder-black";
    placeHolderBgColor = "yellow";
  } else if (colorMode === 2) {
    bgColor = "yellow";
    textColor = "black";
    btnBgColor = "black";
    btnTextColor = "yellow";
    placeHolderColor = "placeholder-yellow";
    placeHolderBgColor = "black";
  } else if (colorMode === 3) {
    bgColor = "";
    textColor = "";
    btnBgColor = "";
    btnTextColor = "";
    placeHolderColor = "";
    placeHolderBgColor = "";
  }

  document.body.style.backgroundColor = bgColor;
  document.body.style.color = textColor;

  const allBtns = document.querySelectorAll("button");
  const allSpans = document.querySelectorAll("span");
  const moreBtn = document.getElementById("more-button");

  const allH2 = document.querySelectorAll("h2");
  const allH3 = document.querySelectorAll("h3");
  const allH4 = document.querySelectorAll("h4");

  const allLabels = document.querySelectorAll("label");
  const allLinks = document.querySelectorAll("a");
  const allStrong = document.querySelectorAll("strong");
  const allP = document.querySelectorAll("p");
  const editAcc_addPostBtns = document.querySelectorAll(
    ".edit-account-button, .add-post-button"
  );
  const postUser_underUser = document.querySelectorAll(
    ".post .post-username, .post .post-under-username, .post .post-content"
  );
  const footer = document.querySelector("footer");
  const scrollToTopBtn = document.getElementById("scrollToTopBtn");

  const logo = document.querySelector("#logo");

  const user = document.querySelector('img[alt="user"]');
  const home = document.querySelector('img[alt="home"]');
  const rules = document.querySelector('img[alt="rules"]');
  const more = document.querySelector('img[alt="more"]');
  const logout = document.querySelector('img[alt="logout"]');
  const search = document.querySelector('img[alt="searching-tool"]');
  const searchIcon = document.getElementById("search-name-icon");

  const dropdownMenu = document.querySelector(".dropdown-menu");

  const place = document.getElementById("login-username");
  const place2 = document.getElementById("login-password");
  const place3 = document.getElementById("register-username");
  const place4 = document.getElementById("register-password");
  const place5 = document.getElementById("register-password-again");
  const place6 = document.getElementById("search-username");
  const place7 = document.getElementById("edit-username");
  const place8 = document.getElementById("desc-content");
  const place9 = document.getElementById("post-content");
  const inputFile = document.getElementById("edit-pfp");
  const addImg = document.getElementById("add-img");

  const places = [
    place,
    place2,
    place3,
    place4,
    place5,
    place6,
    place7,
    place8,
    place9,
  ];

  places.forEach((el) => {
    if (!el) return;
    el.classList.remove(
      "placeholder-white",
      "placeholder-black",
      "placeholder-yellow"
    );
  });

  places.forEach((el) => {
    if (!el) return;

    el.classList.remove(
      "placeholder-white",
      "placeholder-black",
      "placeholder-yellow"
    );

    if (colorMode === 3) {
      el.style.backgroundColor = "";
      el.style.color = "";
    } else {
      el.classList.add(placeHolderColor);
      el.style.backgroundColor = placeHolderBgColor;
      el.style.color = bgColor;
    }
  });

  if (inputFile) {
    inputFile.style.color = textColor;
  }

  if (addImg) {
    addImg.style.color = textColor;
  }

  if (footer) {
    footer.style.backgroundColor = bgColor;
    footer.style.color = textColor;
  }

  allBtns.forEach((btn) => {
    if (!btn) return;
    btn.style.backgroundColor = btnBgColor;
    btn.style.color = btnTextColor;
  });

  allSpans.forEach((span) => {
    if (!span) return;
    span.style.color = textColor;
    if (moreBtn) moreBtn.style.backgroundColor = "";
  });

  allH4.forEach((h4) => {
    if (!h4) return;
    h4.style.color = textColor;
  });

  allH3.forEach((h3) => {
    if (!h3) return;
    h3.style.color = textColor;
  });

  allH2.forEach((h2) => {
    if (!h2) return;
    h2.style.color = textColor;
  });

  allLabels.forEach((label) => {
    if (!label) return;
    label.style.color = textColor;
  });

  allLinks.forEach((link) => {
    if (!link) return;
    link.style.color = textColor;
  });

  allStrong.forEach((strong) => {
    if (!strong) return;
    strong.style.color = textColor;
  });

  allP.forEach((p) => {
    if (!p) return;
    p.style.color = textColor;
  });

  editAcc_addPostBtns.forEach((btn) => {
    if (!btn) return;
    btn.style.backgroundColor = btnBgColor;
    btn.style.setProperty("color", btnTextColor, "important");
  });

  postUser_underUser.forEach((post) => {
    if (!post) return;
    if (colorMode === 2) {
      post.style.color = "white";
    } else {
      post.style.color = textColor;
    }
  });

  if (scrollToTopBtn) {
    scrollToTopBtn.style.backgroundColor = textColor;

    const scroll = scrollToTopBtn.querySelector("img");
    if (scroll) {
      if (colorMode === 2 || colorMode === 3) {
        scroll.src = "images/arrow-up.png";
      } else {
        scroll.src = "images/arrow-up-black.png";
      }
    }
  }

  if (window.location.pathname.includes("rules.html")) {
    const sections = document.querySelectorAll("section");

    sections.forEach((section) => {
      if (!section) return;
      section.style.backgroundColor = textColor;
      section.style.color = bgColor;

      const h2s = document.querySelectorAll("h2");
      h2s.forEach((h2) => {
        if (!h2) return;
        h2.style.color = bgColor;
      });
    });

    if (scrollToTopBtn) {
      scrollToTopBtn.style.borderColor = bgColor;
    }

    const h1 = document.querySelector("h1");
    if (!h1) return;
    h1.style.color = textColor;

    allLinks.forEach((link) => {
      if (!link) return;
      link.style.color = bgColor;
      link.style.backgroundColor = textColor;
    });
  }
  if (user) {
    switch (colorMode) {
      case 0:
        user.src = "images/user-white.png";
        break;
      case 1:
        user.src = "images/user-yellow.png";
        break;
      case 2:
        user.src = "images/user-black.png";
        break;
      default:
        user.src = "images/user.png";
    }
  }
  if (home) {
    switch (colorMode) {
      case 0:
        home.src = "images/home-agreement-white.png";
        break;
      case 1:
        home.src = "images/home-agreement-yellow.png";
        break;
      case 2:
        home.src = "images/home-agreement-black.png";
        break;
      default:
        home.src = "images/home-agreement.png";
    }
  }

  if (rules) {
    switch (colorMode) {
      case 0:
        rules.src = "images/rules-white.png";
        break;
      case 1:
        rules.src = "images/rules-yellow.png";
        break;
      case 2:
        rules.src = "images/rules-black.png";
        break;
      default:
        rules.src = "images/rules.png";
    }
  }

  if (more) {
    switch (colorMode) {
      case 0:
        more.src = "images/more-white.png";
        break;
      case 1:
        more.src = "images/more-yellow.png";
        break;
      case 2:
        more.src = "images/more-black.png";
        break;
      default:
        more.src = "images/more.png";
    }
  }

  if (logout) {
    switch (colorMode) {
      case 0:
        logout.src = "images/logout-white.png";
        break;
      case 1:
        logout.src = "images/logout-yellow.png";
        break;
      case 2:
        logout.src = "images/logout-black.png";
        break;
      default:
        logout.src = "images/logout.png";
    }
  }

  if (search) {
    switch (colorMode) {
      case 0:
        search.src = "images/search-white.png";
        break;
      case 1:
        search.src = "images/search-yellow.png";
        break;
      case 2:
        search.src = "images/search-black.png";
        break;
      default:
        search.src = "images/search.png";
    }
  }

  if (searchIcon) {
    if (colorMode === 0) {
      searchIcon.style.borderColor = "white";
    } else if (colorMode === 1) {
      searchIcon.style.borderColor = "yellow";
    } else if (colorMode === 2) {
      searchIcon.style.borderColor = "black";
    } else {
      searchIcon.style.borderColor = "";
    }
  }

  if (logo) {
    if (colorMode === 2) {
      logo.src = "images/logo2-black.png";
    } else {
      logo.src = "images/logo2.png";
    }
  }

  if (dropdownMenu) {
    dropdownMenu.style.backgroundColor = textColor;

    const links = dropdownMenu.querySelectorAll("a");
    links.forEach((link) => {
      link.style.color = bgColor;
    });
  }

  colorMode = (colorMode + 1) % 4;
};

[zoomIn, zoomOut, webColorBtn].forEach((btn) => {
  Object.assign(btn.style, {
    display: "block",
    margin: "6px 0",
    width: "100%",
    padding: "6px 8px",
    border: "1px solid #aaa",
    borderRadius: "8px",
    cursor: "pointer",
    backgroundColor: "#f0f0f0",
  });
});

accessibilityBtn.onclick = () => {
  menu.style.display = menu.style.display === "none" ? "block" : "none";
};

document.addEventListener("click", function (event) {
  if (
    !accessibilityBtn.contains(event.target) &&
    !menu.contains(event.target)
  ) {
    menu.style.display = "none";
  }
});

menu.appendChild(zoomIn);
menu.appendChild(zoomOut);
menu.appendChild(webColorBtn);

document.body.appendChild(accessibilityBtn);
document.body.appendChild(menu);

updateAccessibilityButtonSize();
window.addEventListener("resize", updateAccessibilityButtonSize);
