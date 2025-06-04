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
let colorMode = 0;

webColorBtn.onclick = () => {
  let bgColor, textColor;

  if (colorMode === 0) {
    bgColor = "black";
    textColor = "white";
  } else if (colorMode === 1) {
    bgColor = "black";
    textColor = "yellow";
  } else if (colorMode === 2) {
    bgColor = "yellow";
    textColor = "black";
  } else if (colorMode === 3) {
    bgColor = "";
    textColor = "";
  }

  document.body.style.backgroundColor = bgColor;
  document.body.style.color = textColor;

  const all = document.querySelectorAll("*");
  all.forEach((el) => {
    el.style.backgroundColor = bgColor;
    el.style.color = textColor;
  });

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
