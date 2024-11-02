document.addEventListener('DOMContentLoaded', () => {
    const optionMenus = document.querySelectorAll(".select-menu");
    optionMenus.forEach(optionMenu => {
        optionMenu.classList.remove("visibility-none");
        optionMenu.classList.add("visible");
    });

    const closeAllDropdowns = () => {
        optionMenus.forEach(optionMenu => {
            optionMenu.classList.remove("active");
            optionMenu.classList.remove("visibility-none");
            optionMenu.classList.add("visible");
        });
    };

    optionMenus.forEach(optionMenu => {
        const selectBtn = optionMenu.querySelector(".select-btn");
        const options = optionMenu.querySelectorAll(".option");
        const sBtnText = optionMenu.querySelector(".sBtn-text");

        selectBtn.addEventListener("click", (event) => {
            closeAllDropdowns();
            optionMenu.classList.toggle("active");
            event.stopPropagation();
        });

        options.forEach(option => {
            option.addEventListener("click", () => {
                let selectedOption = option.querySelector(".option-text").innerText;
                sBtnText.innerText = selectedOption;

                optionMenu.classList.remove("active");
            });
        });
    });

    document.addEventListener('click', () => {
        closeAllDropdowns();
    });
});
