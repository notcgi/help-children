$(document).ready(function() {

    var elemtop = $('.kid-cart .push-content').css('top');
    elemtop = parseInt(elemtop);


    $(window).scroll(function() {
        if ($("#dolgosrochnaya-opeka").length && $("#pjm").length && $("#we-helped").length) {
            if ($(window).scrollTop() >= $("#dolgosrochnaya-opeka").offset().top - 150 && $(window).scrollTop() < $("#pjm").offset().top - 150) {
                $('.cart-nav>div').each(function() {
                    $(this).removeClass("active");
                })
                $('.cart-nav>div:first-child').addClass("active");
            } else if ($(window).scrollTop() >= $("#pjm").offset().top - 150 && $(window).scrollTop() < $("#we-helped").offset().top - 150) {
                $('.cart-nav>div').each(function() {
                    $(this).removeClass("active");
                })
                $('.cart-nav>div:nth-child(2)').addClass("active");
            } else if ($(window).scrollTop() >= $("#we-helped").offset().top - 150) {
                $('.cart-nav>div').each(function() {
                    $(this).removeClass("active");
                })
                $('.cart-nav>div:nth-child(3)').addClass("active");
            }

        }
    });



    $('.cart-nav.kid-profile>div').click(function() {
        var scroll = $(window).scrollTop();
        $('.cart-nav>div').each(function() {
            $(this).removeClass("active");
        });
        $('#letter').removeClass('displaytrue');
        $('#needed').removeClass('displaytrue');
        $('#kid-news').removeClass('displaytrue');

        $(this).addClass("active");
        var link = $(this).find('a').attr('href');
        $(link).addClass('displaytrue');

        if (link == "#needed") {
            var galleryThumbs = new Swiper('.gallery-thumbs', {
                spaceBetween: 10,
                slidesPerView: 4,
                freeMode: true,
                watchSlidesVisibility: true,
                watchSlidesProgress: true,
            });
            var galleryTop = new Swiper('.gallery-top', {
                spaceBetween: 10,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                thumbs: {
                    swiper: galleryThumbs
                }
            });

        }
        $(window).scrollTop(scroll);
    });



    $('.cart-nav.kids-page>div').click(function() {
        /*$('.cart-nav>div').each(function() {
            $(this).removeClass("active");
        })*/
        var $page = $('html, body');
        //$(this).addClass("active");
        var link = $(this).find('a').attr('href');
        $page.animate({
            scrollTop: ($(link).offset().top - 100)
        }, 400);
    });


    $('.dropdown-link').hover(function() {
        $(this).find('.dropdown-content').stop(true, true).delay(100).fadeIn(300);
    }, function() {
        $(this).find('.dropdown-content').stop(true, true).delay(100).fadeOut(300);
    });


    $('.progressline-block .progress-bar').mouseenter(function() {
        $('.aid-card .push-content').css('display', 'none');
        $('.progressline-block .progress-bar').each(function() {
            $('.polygon').detach();
        });

        var progressHeight = $(this).position().top;
        if (progressHeight > 0) {
            $('.aid-card .push-content').css('top', (elemtop + progressHeight) + 'px');
        } else {
            $('.aid-card .push-content').css('top', '-15px');
        }
        var mess = $(this).closest(".progressline-block").find('.push-content');
        var txt = $(this).find('.txt').html();
        mess.html(txt);
        mess.stop().fadeTo(100, 1);
        $(this).append('<div class="polygon"></div>');

        $('.polygon').css('display', 'block');
    })


    $('.push-content').mouseleave(function() {
        $('.polygon').css('display', 'none');
        $('.aid-card .push-content').hide();
    })

    $('.collected-resources__slider .collected-resources').mouseenter(function() {
        $('.newprogressbarwrapper .push-content').css('display', 'none');
        $('.collected-resources__slider .collected-resources').each(function() {
            $('.polygon').detach();
        });

        var progressHeight = $(this).position().top;
        if (progressHeight > 0) {
            $('.newprogressbarwrapper .push-content').css('top', (elemtop + progressHeight) + 'px');
        } else {
            $('.newprogressbarwrapper .push-content').css('top', '-15px');
        }
        var mess = $(this).closest(".newprogressbarwrapper").find('.push-content');
        var txt = $(this).find('.txt').html();
        mess.html(txt);
        mess.stop().fadeTo(100, 1);
        $(this).append('<div class="polygon"></div>');

        $('.polygon').css('display', 'block');
    })

});
