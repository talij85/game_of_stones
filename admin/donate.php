<html>
<head>
<title>Add Donations</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>

<center>
<b>Donations Script</b><br><br><br>

<?php
include("connect.php");

$_POST[email] = str_replace(" ","",$_POST[email]);

if ($_POST[admin] == $admin_username && $_POST[pass] == $admin_password && $_POST[email]) {
  $email = $_POST[email];
  $amount = intval($_POST[amount]);
  $query = "SELECT * FROM donate WHERE email='$email'";
  $result = mysql_query($query, $db);
  $result = mysql_fetch_array($result);
  if ($result[id]) {
    mysql_query("UPDATE donate SET amount='".($result[amount]+$amount)."' WHERE email='$email'");
  }
  else {
    mysql_query("INSERT INTO donate (email, amount) VALUES ('$email', '$amount')");
  }
  echo "<b>Added \$".$amount." to $email - total $".($result[amount]+$amount)."</b><br><br>";
  
  if ($result[amount]+$amount >= 5)
  {
    $resulte = mysql_query("SELECT id, donor, battlestoday FROM Users WHERE email='$email'");
    while ($chare = mysql_fetch_array($resulte)) 
    {
      echo $chare[id]."_".$chare[donor];
      if ($chare[donor] == 0)
      {
        $chare[battlestoday] +=100;	
        mysql_query("UPDATE Users SET donor='1', battlestoday='$chare[battlestoday]' WHERE id='$chare[id]'");
      }
    }
  }
}


?>

<center>
<table><tr><td>
<p align='left'>
<form method="post" action="donate.php">
email: <input type="text" name="email" id="email" maxlength="60">
<br>
amount: <input type="text" name="amount" value="5" id="amount" maxlength="60" size="5">
<br>
<br>
admin name: <input type="text" name="admin" value="<?php echo $_POST[admin]; ?>" id="admin" maxlength="30" size="7">
<br>
admin pass: <input type="password" name="pass" value="<?php echo $_POST[pass]; ?>" id="pass" maxlength="30" size="7">
</td></tr></table>

<br>
<input type="submit" name="submit" value="Add">
</form>

</body>
</html>