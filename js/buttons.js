document.addEventListener("DOMContentLoaded", () => {

    const buttons = document.querySelectorAll(".button-s, .button-l, .ask-button, .submit-button, .avatar-dropdown a, .contact-link");

    buttons.forEach(button => {

        button.addEventListener("mousedown", () => {
            button.classList.add("pressed");
        });

        button.addEventListener("mouseup", () => {
            button.classList.remove("pressed");
        });

        button.addEventListener("mouseleave", () => {
            button.classList.remove("pressed");
        });

    });

});


const avatar = document.querySelector(".avatar");
const avatarIcon = document.querySelector(".avatar-icon");

if (avatar && avatarIcon) {
    avatarIcon.addEventListener("click", () => {
        avatar.classList.toggle("open");
    });
}

document.addEventListener("click", (e) => {
    if (!avatar.contains(e.target)) {
        avatar.classList.remove("open");
    }
});