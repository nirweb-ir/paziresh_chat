
jQuery(document).ready(function ($) {

// باز شدن منوی همبرگری برای مشاهده chat های دیگر


    // open

    $(".show_ather_chat svg").click(function () {
        $(".menu_show_cart_pv").addClass("show");
        $(".screen_black").addClass("active");
    })

    // close

    $(".screen_black").click(function (e) {
        $(".menu_show_cart_pv").removeClass("show");
        $(".screen_black").removeClass("active");
    })

    $(".chat-list .chat-item").click(function (e) {
        $(".menu_show_cart_pv").removeClass("show");
        $(".screen_black").removeClass("active");
    })



})

