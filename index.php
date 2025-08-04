<?php
include "./header.php";


// -------------------------------------------
// pupop check to connect

include "./template/check_connect/connect_check_pupop.php";

// -------------------------------------------
// connect to sucket

include "./template/check_connect/connect_sucket.php";

// -------------------------------------------
// chat space

echo "<div class='container'>";
echo "<div class='screen_black'></div>";

include "./template/chat/show_user.php";
include "./template/chat/show_text.php";

echo "</div>";

include "./footer.php";
?>