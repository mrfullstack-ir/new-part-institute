function showSlider() {
    $('.slider').slick({
        infinite: true,
        slidesToShow: 4,
        slidesToScroll: 1,
        rtl : true,
        prevArrow : document.getElementsByClassName("category-right-button")[0] ,
        nextArrow : document.getElementsByClassName("category-left-button")[0],
    });
}

function main() {
    showSlider()
}

main()