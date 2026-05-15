const errorData = document.getElementById("error-data");

if (errorData) {

    const message = errorData.dataset.message;
    const type = errorData.dataset.type || "error";

    const popup = document.createElement("div");
    popup.className = "popup";

    // Add type class
    popup.classList.add(`popup-${type}`);

    const closeBtn = document.createElement("span");
    closeBtn.className = "popup-close";
    closeBtn.innerHTML = "&times;";

    popup.appendChild(closeBtn);
    popup.appendChild(document.createTextNode(message));

    document.body.appendChild(popup);

    const displayDuration = 5000;
    const fadeDuration = 5000;

    closeBtn.addEventListener("click", () => {
        popup.remove();
    });

    setTimeout(() => {
        popup.style.transition = `opacity ${fadeDuration}ms ease`;
        popup.style.opacity = "0";

        setTimeout(() => {
            popup.remove();
        }, fadeDuration);

    }, displayDuration);
}