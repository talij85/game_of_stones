<?php
	include("admin/connect.php");
	$id = $_COOKIE['id'];
	$password = $_COOKIE['password'];
	$time=time();
	$result = mysql_query("SELECT * From Users WHERE id='$id' AND password='$password'");
	if (mysql_fetch_row($result))
	{
		header("Location: $server_name/bio.php?autologin=$time");
	}
	else
	{
		header("Location: $server_name/index2.php?time=$time");
	}
?>