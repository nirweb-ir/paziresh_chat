<?php



function func_user_pv( $active = false , $first_car_name = "" , $name = "" , $massage = "" , $counter_massage = null , $time = null )
{
    ?>
    <div class="chat-item  <?= $active == true ? "active" : ""  ?> " data-chat="4">
        <div class="chat-avatar">
            <?= $first_car_name ?>
        </div>
        <div class="chat-info">
            <div class="chat-name"><?= $name ?></div>
            <div class="chat-last-message"><?= $massage ?></div>
        </div>
        <div class="chat-meta">

            <?php
                if ( $time != null ) {
                    ?> <div class="chat-time"><?= $time ?></div> <?php
                }
                if ( $counter_massage != null ) {
                    ?> <div class="unread-badge"><?= $counter_massage ?></div> <?php
                }
            ?>

        </div>
    </div>
    <?php
}

?>

