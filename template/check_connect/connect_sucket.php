<script>
    jQuery(document).ready(function ($) {


        // const url_connect_to_socket = "https://socketio.darkube.app";
        const url_connect_to_socket = "http://localhost:3000";

        const socket = io(url_connect_to_socket, {
            transports: ['websocket'],
            timeout: 5000,
            reconnectionAttempts: 1,
        });

        socket.on('connect', () => {
            mode_connect_func("suc");
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

        function mode_connect_func( mode_connect ) {

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

                    setTimeout( function () {
                        $(".container_check_connection").removeClass("active");
                    } , 1000)

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


        // ----------------------------------------------------------
        // get data to socket
        // ----------------------------------------------------------

        array_user_pv = [];

        socket.on('new-message', (data) => {


            // get_message

            let conversations_message = data.conversations;
            conversations_message.forEach( function(item,key) {
                let user_pv = {
                    name: item.sender_name,
                    id: item.sender_id,
                    messages: [
                    ]
                }
                item.new_messages.forEach( function(item_new_messages,key_new_messages) {
                    user_pv.messages.push({
                        text_message: item_new_messages.text,
                        status: "1",
                        message_id: item_new_messages.message_id,
                    });
                });
                item.old_messages.forEach( function(item_old_messages,key_old_messages) {
                    user_pv.messages.push({
                        text_message: item_old_messages.text,
                        status: "0",
                        message_id: item_old_messages.message_id,
                    });
                });
                array_user_pv.push(user_pv);
            })
            console.log(array_user_pv)

            // ----------------------
            // creat pv

            array_user_pv.forEach(function ( itme , key ) {

                let count_message = 0;
                let last_message = "";
                itme.messages.forEach(function (itme_message , key_message) {
                    if ( itme_message.status == 1 ) {
                        count_message +=1;
                        last_message = itme_message.text_message;
                    }
                })
                creat_vp( itme.name , itme.id , last_message , count_message )

            } )






        });







    // ----------------------------------------------------------
    // functions
    // ----------------------------------------------------------

    //  creat pv

        function creat_vp( name_vp="" , id_vp=-1 , lase_message="", number_new_massage="" ) {
            let target = $(".menu_show_cart_pv .chat-list");
            let pv_html =  `<div class="chat-item" id_pv="${id_vp}" data-chat="4">
                                <div class="chat-avatar"> ${ name_vp[0] || "N" } </div>
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
        
        function have_pv_or_no () {

        }


        

    })
</script>

