<?php
    include "./template/chat/show_text/header_pv.php";
    include "./template/chat/show_text/message.php";
?>

<div class="chat-area">

    <?php
        func_header_pv( "f" , "علی پارسا" );
    ?>

    <div class="messages-container" id="messagesContainer">

        <?php
            func_message( "received" , "سلام، حالت چطوره؟" , "20:30" );
            func_message( "send" , "سلام، حالت چطوره؟" , "20:30" );
            func_message( "received" , "سلام، حالت چطوره؟" , "20:30" );
            func_message( "send" , "سلام، حالت چطوره؟" , "20:30" );
        ?>

    </div>

    <div class="input-area">
        <textarea class="message-input" id="messageInput" placeholder="پیام خود را بنویسید..." rows="1"></textarea>
        <button class="send-button" id="sendButton">
            ➤
        </button>
    </div>

</div>