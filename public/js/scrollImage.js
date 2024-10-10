window.addEventListener("scroll", function () {
    const elements = document.querySelectorAll(".zoom");

    elements.forEach((element) => {
        const elementPosition = element.getBoundingClientRect().top;
        const windowHeight = window.innerHeight;

        if (elementPosition < windowHeight) {
            element.classList.add("element-visible");
        }
    });

});
