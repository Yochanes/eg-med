$(document).ready(function () {
    $('.banner-blk').appendTo('.be1');
});
/* responsive menu */
function openNav() {
    $('body').addClass("active");
    document.getElementById("mySidenav").style.width = "280px";
}
function closeNav() {
    $('body').removeClass("active");
    document.getElementById("mySidenav").style.width = "0";
}


 /* loader */
$(window).load(function myFunction() {
  $(".s-panel .loader").removeClass("wrloader");
});

//go to top
$(document).ready(function () {
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('#scroll').fadeIn();
        } else {
            $('#scroll').fadeOut();
        }
    });
    $('#scroll').click(function () {
        $("html, body").animate({scrollTop: 0}, 600);
        return false;
    });
});


$(document).ready(function () {
    if ($(window).width() <= 991) {
    $('.menusp').appendTo('.appmenu');
}
});


function openSearch() {
    $('body').addClass("active-search");
    document.getElementById("search").style.height = "auto";
    $('#search').addClass("sideb");
    $('.search_query').attr('autofocus', 'autofocus').focus();
}
function closeSearch() {
    $('body').removeClass("active-search");
    document.getElementById("search").style.height = "0";
    $('#search').addClass("siden");
    $('.search_query').attr('autofocus', 'autofocus').focus();
}

// search dropdwon
$(document).ready(function () {
$(".position-static .search-down").click(function(){
    $('.position-static .searchtg').parent().toggleClass('active');
    $('.position-static .searchtg').toggle('slow',function(){});
    $('.position-static .ui-autocomplete-input').attr('autofocus','autofocus').focus()});
});
// end

$(document).ready(function () {
$("#ratep,#ratecount").click(function() {
    $('body,html').animate({
        scrollTop: $(".product-tab").offset().top 
    }, 1500);
});
});

$(document).ready(function () {
$('.dropdown button.test').on("click", function(e)  {
    $(this).next('ul').toggle();
    e.stopPropagation();
    e.preventDefault();
});
 
});


/* dropdown effect of account */
$(document).ready(function () {
    if ($(window).width() <= 767) {
    $('.catfilter').appendTo('.appres');

    $('.dropdown a.account').on("click", function(e) {
        $(this).next('ul').toggle();
        e.stopPropagation();
        e.preventDefault();
    });
}
});
/* dropdown */
$(document).ready(function () {
    if ($(window).width() > 767) {
   $('.webi-newsletter').appendTo('.about-block .container');
}
});
/* sticky header */
  if ($(window).width() > 992) {
    //  $(document).ready(function(){
    //
    //       $(window).scroll(function () {
    //         if ($(this).scrollTop() > 100) {
    //             $('.hbottom').addClass('fixed fadeInDown animated');
    //         } else {
    //             $('.hbottom').removeClass('fixed fadeInDown animated');
    //         }
    //       });
    //
    // });
  };

$(document).ready(function(){
    if ($(window).width() >= 1600){
    var number_blocks = 5;
    var count_block = $('#menu .m-menu');
    var moremenu = count_block.slice(number_blocks, count_block.length);
    moremenu.wrapAll('<li class="view_cat_menu tab-menu"><div class="more-menu sub-menu">');
    $('.view_cat_menu').prepend('<a href="#" class="submenu-title">More</a>');
    $('.view_cat_menu').mouseover(function(){
    $(this).children('div').show();
    })
    $('.view_cat_menu').mouseout(function(){
    $(this).children('div').hide();
    });
    };
});

$(document).ready(function(){
    if ($(window).width() >= 1410 && $(window).width() <= 1589){
    var number_blocks = 4;
    var count_block = $('#menu .m-menu');
    var moremenu = count_block.slice(number_blocks, count_block.length);
    moremenu.wrapAll('<li class="view_cat_menu tab-menu"><div class="more-menu sub-menu">');
    $('.view_cat_menu').prepend('<a href="#" class="submenu-title">More</a>');
    $('.view_cat_menu').mouseover(function(){
    $(this).children('div').show();
    })
    $('.view_cat_menu').mouseout(function(){
    $(this).children('div').hide();
    });
    };
});


$(document).ready(function(){

    if ($(window).width() >= 992 && $(window).width() <= 1409){
    var number_blocks =4;
    var count_block = $('#menu .m-menu');
    var moremenu = count_block.slice(number_blocks, count_block.length);
    moremenu.wrapAll('<li class="view_cat_menu tab-menu"><div class="more-menu sub-menu">');
    $('.view_cat_menu').prepend('<a href="#" class="submenu-title">More</a>');
    $('.view_cat_menu').mouseover(function(){
    $(this).children('div').show();
    })
    $('.view_cat_menu').mouseout(function(){
    $(this).children('div').hide();
    });
    };
});

$(document).ready(function(){
    if ($(window).width() <= 767) {
        $('.copy').insertAfter('.news');
    };
});

$(document).ready(function(){
    if ($(window).width() <= 575) {
        $('#search').insertAfter('#top-links');
    };
});