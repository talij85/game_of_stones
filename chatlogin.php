<?php
// register nickname as session variable
// & redirect to chat page
if($_POST['enter']){
	session_start();
	$_SESSION['user']=mysql_real_escape_string($_POST['nickname']);
	header('location:ajaxchat.php');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>CHAT LOGIN</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<style type="text/css">
body {
	background: #eee;
	margin: 0px;
	padding: 0px;
}
h1 {
	font: bold 36px Arial, Helvetica, sans-serif;
	text-align: center;
}
div {
	width: 50%;
	background: #99f;
	border: 1px solid #000;
	padding: 20px;
	margin-left: auto;
	margin-right: auto;
}
p {
	font: normal 12px Verdana, Arial, Helvetica, sans-serif;
	color: #000;
}
</style>
<script language="javascript">
window.onload=function(){
	var nick=document.getElementsByTagName('form')[0].elements['nickname'];
	if(!nick){return};
	nick.focus();
}
</script>
</head>
<body>
<h1>AJAX-BASED CHAT SYSTEM</h1>
<div>
<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
<p>Enter your nickname <input type="text" name="nickname" />
<input type="submit" name="enter" value="Go!" /></p>
</form>
</div>
</body>
</html>