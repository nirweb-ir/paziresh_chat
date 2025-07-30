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

        arr_users_pv = [];
        user_new_message = [];


        massage = [];

        socket.on('new-message', (data) => {

            console.log(data);
            console.log(data.recepient_id);
            console.log(data.recepient_name);

            let conversations_message = data.conversations;

            conversations_message.forEach( function ( item , key ) {
                console.log( item.sender_id )
                arr_users_pv.push(item.sender_id);
            } )


            console.log(arr_users_pv)







            let place = $(".messages-container");



        });



    })
</script>