function loadStickyNavigation() {
    const nav = document.querySelector("nav")
    const header = document.querySelector('header');

    /**
     * @function
     * @description این فانکشن وظیفه ایجاد یک منوی چسبان را بر عهده دارد
     */
    function toggleSticky() {
        if (window.pageYOffset >= header.offsetHeight) {
            nav.classList.add("nav-dropdown-animate");
            nav.style.backgroundColor = "#131313";
            nav.style.paddingBottom = "30px";
            nav.style.position = "fixed";
            nav.style.width = "100%";
            nav.style.zIndex = "1";
            nav.classList.remove("nav-back-animate");
        } else {
            nav.classList.add("nav-back-animate");
            setTimeout(() => {
                nav.style.backgroundColor = "unset";
                nav.style.paddingTop = "30px";
                nav.style.paddingRight = "55px";
                nav.style.paddingLeft = "55px";
                nav.style.position = "unset";
                nav.classList.remove("nav-dropdown-animate");
            }, 499);
        }
    }

    window.addEventListener("scroll", toggleSticky);
}

function showSlider() {
    $('.slider').slick({
        infinite: true,
        slidesToShow: 4,
        slidesToScroll: 1,
        rtl: true,
        prevArrow: document.getElementsByClassName("category-right-button")[0],
        nextArrow: document.getElementsByClassName("category-left-button")[0],
        responsive: [
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });
}

function main() {
    showSlider()
    loadStickyNavigation()
}

main()