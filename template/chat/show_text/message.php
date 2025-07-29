<?php

function func_message($received_or_sender = "received", $text_message = "", $time = null)
{
    ?>

    <div class="message <?= $received_or_sender == "received" ? "received" : "sent" ?>">
        <div class="message-bubble">
            <?= $text_message ?>

            <?php
            if ($time != null) {
                ?>
                <div class="message-time"> <?= $time ?> </div>
                <?php
            }
            ?>

        </div>
    </div>


    <?php
}

?>




