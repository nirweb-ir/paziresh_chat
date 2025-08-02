
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


// دکتر وقتی که پیام ارسال میکنه

    // نوشتن پیام

    $(".send-button").on("click", function (e) {
        let Error = false;

        let pv_focus = $(".chat-header").attr("id_pv");
        let text = $(".message-input").val();

        // بررسی خالی بودن
        if (!pv_focus || !text.trim()) {
            Error = true;
        }

        if ( Error != true ) {
            create_messahe(text);
            $(".message-input").val("");
        }

    });


    // ساخت message و انتقال به پایین
    function create_messahe ( message ) {

        let Message__ = ` <div class="message sent">
                                    <div class="message-bubble">
                                            ${message}
                                    </div>
                                 </div>
                                `;

        $(".messages-container").append( Message__ );

        let container = document.querySelector('.messages-container');
        container.scrollTo({
            top: container.scrollHeight,
            behavior: "smooth"
        });


        array_user_pv = JSON.parse( sessionStorage.getItem('array_user_pv') );
        console.log(array_user_pv);

    }


})



