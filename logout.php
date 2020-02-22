<?php
include('admin/connect.php');

setcookie("id", "", time()-60, "/");
setcookie("name", "", time()-60, "/");
setcookie("lastname", "", time()-60, "/");
setcookie("password", "", time()-60, "/");


header("Location: $server_name/index2.php");
?>