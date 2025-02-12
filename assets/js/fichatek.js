document.addEventListener("DOMContentLoaded", () => {
    const openers = document.querySelectorAll(".opener");

    openers.forEach(opener => {
        opener.addEventListener("click", () => {
            // Encuentra los paneles relacionados
            let nextRow = opener.nextElementSibling;
            while (nextRow && nextRow.classList.contains("hidden-panel")) {
                nextRow.classList.toggle("open");
                nextRow = nextRow.nextElementSibling;
            }
        });
    });
});