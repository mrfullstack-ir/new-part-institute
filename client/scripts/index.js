function showSlider() {
    $('.slider').slick({
        infinite: true,
        slidesToShow: 4,
        slidesToScroll: 1,
        rtl : true,
        prevArrow : document.getElementsByClassName("category-right-button")[0] ,
        nextArrow : document.getElementsByClassName("category-left-button")[0],
        responsive:[
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
}

main()