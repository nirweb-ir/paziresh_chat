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


            $(".container_check_connection #iconSymbol").html("✓");
            $(".container_check_connection .status-icon").addClass("connected");
            $(".container_check_connection .status-message").html("اتصال با موفقیت انجام شد");



            setTimeout(function () {
                $(".container_check_connection #iconSymbol").html("📩");
                $(".container_check_connection .status-icon").addClass("get_message");
                $(".container_check_connection .status-message").html("درحال دریافت پیام ها");

                //  در خواست به n8n برای این که چت هارا ارسال کند
                callApi(
                    'https://n8n.nirweb.ir/webhook/get_first_chat',
                    'POST',
                    { user_id: user_id_connect_to_socket, type_res: 'get_all_message' }
                )
                .then(data => {
                    if (data.res && data.res.length > 0) {
                        if ( data.res == "fail" ) {
                            mode_connect_func("Err");
                        }
                    }
                })
                .catch(err => console.error(err));

            }, 1000)


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
            $(".container_check_connection .status-icon").removeClass("get_message");

            switch (mode_connect) {

                case "Err":

                    $(".container_check_connection #iconSymbol").html("✗");
                    $(".container_check_connection .status-icon").addClass("disconnected");
                    $(".container_check_connection .status-message").html("اتصال ناموفق مجدد تلاش کنید");

                    break;

                case "suc":

                    // $(".container_check_connection #iconSymbol").html("✓");
                    // $(".container_check_connection .status-icon").addClass("connected");
                    // $(".container_check_connection .status-message").html("اتصال با موفقیت انجام شد");
                    // setTimeout(function () {
                    //     $(".container_check_connection #iconSymbol").html("📩");
                    //     $(".container_check_connection .status-icon").addClass("get_message");
                    //     $(".container_check_connection .status-message").html("درحال دریافت پیام ها");
                    // }, 1000)

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

            if ( Object.keys(data).length > 0)
            {

                if ( data.message_mode == "preliminary_report" )
                {

                    $(".container_check_connection").removeClass("active");

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

                }
                else if ( data.message_mode == "new_message" )
                {
                    // دریافت پیام جدید

                    let array_user_pv =  JSON.parse(sessionStorage.getItem('array_user_pv'));

                    // اگر خالی نبود

                    if (!array_user_pv || array_user_pv.length === 0) { } else {

                        let sender_id = data.conversations[0].sender_id;
                        let sender_name = data.conversations[0].sender_name;
                        let sender_message = data.conversations[0].messages;

                        let _message_id_ = sender_message[0].message_id;
                        let _text_ = sender_message[0].text;
                        let _timestamp_ = sender_message[0].timestamp;
                        let _new_or_old_ = sender_message[0].new_or_old;
                        let _role_ = sender_message[0].role;

                        let find_pv = false;

                        array_user_pv.forEach( function ( item_ ,  key_ ) {
                            if ( sender_id == item_.id ) {

                                find_pv = true;

                                item_.messages.push({
                                    message_id: _message_id_,
                                    text: _text_,
                                    timestamp: _timestamp_,
                                    new_or_old: _new_or_old_,
                                    role: _role_
                                });
                            }
                        })

                        sessionStorage.setItem('array_user_pv' , JSON.stringify(array_user_pv));

                        // اگر پیوی وجود داشت
                        // اگر وجود نداشت یک پیوی بساز
                        if( find_pv == true ) {

                            // بررسی این که ایا فکوس هیتیم روی پیوی یا خیر
                            // اگر فوکوس نکردی دانتر براش بنداز

                            var id_pv = $(".chat-header").attr("id_pv");
                            if (id_pv == sender_id)
                            {
                                creat_message ( _role_ , _text_ , _new_or_old_ , _message_id_, sender_id )
                            }
                            else
                            {
                                const badgeHtml = $(`.chat-item[id_pv="${sender_id}"] .unread-badge`).html();
                                let unreadCount = Number(badgeHtml) || 0;
                                unreadCount += 1;
                                $(`.chat-item[id_pv="${sender_id}"] .unread-badge`).html(unreadCount);

                                hiden_finction_pv ( sender_id )
                            }

                        } else {

                            let array_user_pv =  JSON.parse(sessionStorage.getItem('array_user_pv'));

                            array_user_pv.push({
                                name: sender_name,
                                id: sender_id,
                                messages: []
                            })

                            array_user_pv.forEach(function (item , key) {
                                if ( item.id == sender_id )  {
                                    item.messages.push({
                                        message_id: _message_id_,
                                        text: _text_,
                                        timestamp: _timestamp_,
                                        new_or_old: _new_or_old_,
                                        role: _role_
                                    })
                                }
                            })

                            sessionStorage.setItem('array_user_pv' , JSON.stringify(array_user_pv));

                            creat_vp( sender_name , sender_id , _text_ , 1 )
                        }

                    }
                }
            }
            else { console.log("درخواست خالی است"); }

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


            hiden_finction_pv ( id_vp )
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


            let save_id_message_from_sort = [];
            let save_id_message_text = [];

            // بررسی چت ها در ارایه

            array_user_pv.forEach(function (item, key) {
                // پیدا کردن مقادیر یوزر
                if (item.id == id.trim()) {
                    // ارسال پیام های
                    item.messages.forEach(function (item_message, key_message) {
                        save_id_message_from_sort.push( item_message.message_id );
                        save_id_message_text.push( item_message );
                    })
                }
            })


            // ارسال پیام بر اساس سورت

            save_id_message_from_sort.sort((a, b) => a - b);

            for ( i=0 ; i<save_id_message_from_sort.length ; i++ ) {
                for ( e=0 ; e<save_id_message_text.length ; e++ ) {
                    if ( save_id_message_text[e].message_id == save_id_message_from_sort[i] ) {
                        creat_message( save_id_message_text[e].role , save_id_message_text[e].text , save_id_message_text[e].new_or_old , save_id_message_text[e].message_id , id );
                        break;
                    }
                }
            }

            // بعد از این که صفحه جت بره پایین ترین قسمت اکرول بشه
            var $container = $('.messages-container');
            if ($container.length) {
                $container.scrollTop($container[0].scrollHeight);
            }

            hiden_finction_pv(id);
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
                )
                .then(data => console.log(data) )
                .catch(err => console.error(err));
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
            
            //  حرکت نرم به پایین ترین قسمت

            const container = document.querySelector('.messages-container');
            if (container) {
                container.scrollTo({
                    top: container.scrollHeight,
                    behavior: 'smooth'
                });
            }
        }

        // --------------------------------------------------------------------------------------------------------------------
        // hiden counter pv
        // --------------------------------------------------------------------------------------------------------------------

        function hiden_finction_pv ( id_pv ) {

            let counter = $(`.chat-item[id_pv=${id_pv}]`).find(".unread-badge").html();
            let countNumber = Number((counter || '').trim()) || 0;

            if ( countNumber == 0 ) {
                $(`.chat-item[id_pv=${id_pv}]`).find(".unread-badge").addClass("background_number");
            } else {
                $(`.chat-item[id_pv=${id_pv}]`).find(".unread-badge").removeClass("background_number");
            }

        }
        
    })


</script>
