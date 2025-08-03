<script>
    jQuery(document).ready(function ($) {


        user_id_connect_to_socket = "6eb14e4f-e0dc-4497-b327-c525ae962338";

        // save id in section
        sessionStorage.setItem('id_client',user_id_connect_to_socket );



        const url_connect_to_socket = "https://socketchat.darkube.app";
        // const url_connect_to_socket = "http://localhost:3000";

        const socket = io(url_connect_to_socket, {
            transports: ['websocket'],
            timeout: 5000,
            reconnectionAttempts: 3,
        });

        socket.on('connect', () => {
            socket.emit('register', user_id_connect_to_socket);
            mode_connect_func("suc");


            //  در خواست به n8n برای این که چت هارا ارسال کند
            callApi(
                'https://n8n.nirweb.ir/webhook/get_first_chat',
                'POST',
                { user_id: user_id_connect_to_socket, type_res: 'get_all_message' }
            ).catch(err => console.error(err));

                // .then(data => console.log(data))
                // .catch(err => console.error(err));

        });

        socket.on('connect_error', (error) => {
            mode_connect_func("Err");
        });

        socket.on('connect_timeout', () => {
            mode_connect_func("time_out");
        });

        socket.on('disconnect', (reason) => {
            mode_connect_func("cansel");
        });

        function mode_connect_func(mode_connect) {

            $(".container_check_connection .status-icon").removeClass("connected");
            $(".container_check_connection .status-icon").removeClass("disconnected");
            $(".container_check_connection .status-icon").removeClass("connecting");

            switch (mode_connect) {

                case "Err":

                    $(".container_check_connection #iconSymbol").html("✗");
                    $(".container_check_connection .status-icon").addClass("disconnected");
                    $(".container_check_connection .status-message").html("اتصال نا موفق مجدد تلاش کنید");

                    break;

                case "suc":
                    $(".container_check_connection #iconSymbol").html("✓");
                    $(".container_check_connection .status-icon").addClass("connected");
                    $(".container_check_connection .status-message").html("اتصال با موفقیت انجام شد");

                    setTimeout(function () {
                        $(".container_check_connection").removeClass("active");
                    }, 1000)

                    break;

                case "time_out":

                    $(".container_check_connection #iconSymbol").html("✗");
                    $(".container_check_connection .status-icon").addClass("disconnected");
                    $(".container_check_connection .status-message").html("time out ...");

                    break;

                case "cansel":

                    $(".container_check_connection").addClass("active");

                    $(".container_check_connection #iconSymbol").html("✗");
                    $(".container_check_connection .status-icon").addClass("disconnected");
                    $(".container_check_connection .status-message").html("اتصال شما قصع شد صفحه را رفرش کنید ");

                    break;
            }


        }


        // --------------------------------------------------------------------------------------------------------------------
        // get data to socket
        // --------------------------------------------------------------------------------------------------------------------

        array_user_pv = [];

        socket.on('new-message', (data) => {

            if ( Object.keys(data).length > 0) {

                // ----------------------
                // get message and add to array array_user_pv

                let conversations_message = data.conversations;
                conversations_message.forEach(function (item, key) {

                    // اگر پیوی وجود داشت اون را حذف کن
                    var index = array_user_pv.findIndex(function (item_find) {
                        return item_find.id == item.sender_id;
                    });
                    if (index > -1) {
                        array_user_pv.splice(index, 1);
                    }

                    // اطلاعات pv
                    let user_pv = {
                        name: item.sender_name,
                        id: item.sender_id,
                        messages: []
                    }

                    item.messages.forEach(function (item_new_messages, key_new_messages) {
                        user_pv.messages.push({
                            message_id: item_new_messages.message_id,
                            text: item_new_messages.text,
                            timestamp: item_new_messages.timestamp,
                            new_or_old: item_new_messages.new_or_old,
                            role: item_new_messages.role
                        });
                    });
                    array_user_pv.push(user_pv);
                })

                // ----------------------
                // creat pv

                array_user_pv.forEach(function (itme, key) {

                    let count_message = 0;
                    let last_message = "";

                    itme.messages.forEach(function (itme_message, key_message) {
                        if (itme_message.new_or_old == 1  &&  itme_message.role == "requester" ) {
                            count_message += 1;
                            last_message = itme_message.text;
                        }
                    })

                    let have_pv_or_no = getChatItemByIdPv(itme.id);
                    if (have_pv_or_no == null) {
                        creat_vp(itme.name, itme.id, last_message, count_message)
                    }
                    else {
                        edite_pv(have_pv_or_no, count_message, last_message)
                    }

                })

                // ----------------------
                // ذخیره کردن سشن

                sessionStorage.setItem('array_user_pv', JSON.stringify(array_user_pv) );

            } else {
                console.log("درخواست خالی است");
            }

        });

        // --------------------------------------------------------------------------------------------------------------------
        // functions
        // --------------------------------------------------------------------------------------------------------------------

        //  creat pv

        function creat_vp(name_vp = "", id_vp = -1, lase_message = "", number_new_massage = "") {
            let target = $(".menu_show_cart_pv .chat-list");
            let pv_html = `<div class="chat-item" id_pv="${id_vp}" data-chat="4">
                                <div class="chat-avatar"> ${name_vp[0] || "N"} </div>
                                <div class="chat-info">
                                    <div class="chat-name"> ${name_vp} </div>
                                    <div class="chat-last-message"> ${lase_message} </div>
                                </div>
                                <div class="chat-meta">
                                   <div class="unread-badge">${number_new_massage} </div>
                                </div>
                            </div>`;
            target.append(pv_html)
        }

        // ایا پیوی وجود دارد یا خیر

        function getChatItemByIdPv(idPv) {
            var item = $('#sidebar .chat-item[id_pv="' + idPv + '"]');
            return item.length ? item : null;
        }

        // اپدیت pv

        function edite_pv(target_pv, numbrt_message, last_message) {
            target_pv.find(".unread-badge").html(numbrt_message);
            target_pv.find(".chat-last-message").html(last_message);
        }


        // --------------------------------------------------------------------------------------------------------------------
        // switch pv
        // --------------------------------------------------------------------------------------------------------------------

        $(".menu_show_cart_pv").on("click", ".chat-item", function (e) {

            $(".messages-container").html("");

            // دریافت ایدی و نام
            let id = $(this).attr("id_pv");
            let name = $(this).find(".chat-name").html();

            // قرار داد نام یوزر در هدر
            $(".chat-area .chat-header-info h3").html(name);
            $(".chat-area .chat-header-avatar").html(name.trim()[0]);
            $(".chat-header").attr("id_pv", id);

            $(this).find(".unread-badge").html("");


            array_user_pv = JSON.parse( sessionStorage.getItem('array_user_pv') );

            // بررسی چت ها در ارایه
            array_user_pv.forEach(function (item, key) {

                // پیدا کردن مقادیر یوزر
                if (item.id == id.trim()) {

                    // ارسال پیام های
                    item.messages.forEach(function (item_message, key_message) {
                        creat_message( item_message.role , item_message.text , item_message.new_or_old , item_message.message_id , id );
                    })

                    // بعد از این که صفحه جت بره پایین ترین قسمت اکرول بشه
                    var $container = $('.messages-container');
                    if ($container.length) {
                        $container.scrollTop($container[0].scrollHeight);
                    }

                }
            })

        })

        // --------------------------------------------------------------------------------------------------------------------
        // function sin query
        // --------------------------------------------------------------------------------------------------------------------

        function creat_message ( role , text , new_or_old , message_id, id_pv="" ) {

            //  ساخت پیام

            let message_box = "";
            if (role === "requester") {
                message_box = `<div class="message received"> <div class="message-bubble"> ${text} </div> </div>`;
            } else {
                message_box = `<div class="message sent"> <div class="message-bubble"> ${text} </div> </div>`;
            }

            //  اپند کردن پیام

            $(".messages-container").append(message_box);

            //  api سین کردن پیام

            if ( new_or_old == true && role == "requester" ) {
                callApi(
                    'https://n8n.nirweb.ir/webhook/sin_message',
                    'POST',
                    { id_message: message_id }
                ).then(data => console.log(data)).catch(err => console.error(err));
            }

            //  پیام را به سین شده اپدیت میکنه

            array_user_pv = JSON.parse( sessionStorage.getItem('array_user_pv') );

            array_user_pv.forEach( function ( item_find_pv , key_find_pv ) {
                if ( item_find_pv.id == id_pv ) {
                    item_find_pv.messages.forEach(function ( item_update_message , key_update_message ) {
                        if ( item_update_message.message_id == message_id  && item_update_message.role == "requester" ) {
                            item_update_message.new_or_old = false;
                        }
                    })
                }
            })

            sessionStorage.setItem('array_user_pv' , JSON.stringify(array_user_pv) )

        }
    })


</script>
