<?php

// connect to MySQL
require_once ("mysql_class.php");
include_once ("admin/connect.php");
include_once ("admin/userdata.php");
// get user & message
$write=1;
$id=mysql_real_escape_string($_GET['id']);
$jtime=time();
// insert new message into database table
$msgs = mysql_fetch_array(mysql_query("SELECT * FROM messages WHERE id='".$id."'"));
$msg = unserialize($msgs[message]);

if (!($name && $lastname && $password) || $password!=$char['password']) 
{
  $newmsg = "<Creators_voice_channeler_".time().">` You are not logged in!|";
  $write = 0;
}
else
{
$user=$char[name]."_".$char[lastname];
if($char['goodevil']==3) $classn="warning";
else if ($char['donor']==1) $classn="danger";
else $classn="primary";
$message=$_POST['message'];
$message = str_replace("\\","",$message);
$message = str_replace("`","'",$message);
$message = str_replace("|",":",$message);
$message = htmlspecialchars(stripslashes($message));
$newmsg = '<'.$user.'_btn-'.$classn.'_'.$jtime.'>` '.$message.'|';
$newmsg = str_replace("'",'-ap-',$newmsg);

}// end else

$msg[count($msg)]=$newmsg;

if (count($msg) > 200)
{
  for ($i = 0; $i < (count($msg)-1); $i++)
  {
    $msg[$i]=$msg[$i+1];
  }
  array_pop($msg);
}
if ($msg[0])
{
  $msgs= serialize($msg);

  if ($write)
  $result = mysql_query("UPDATE messages SET message='$msgs' WHERE id='".$id."'", $db);
  if ($id == 0) {
    mysql_query("UPDATE Users_stats SET tar_posts = tar_posts + 1 WHERE id='".$char[id]."'");
  } else {
    mysql_query("UPDATE Users_stats SET clan_posts = clan_posts + 1 WHERE id='".$char[id]."'");
  }

// send messages to the client
  for ($i = 0; $i < count($msg); $i++)
  {
    $output = str_replace("-ap-","'",$msg[$i]);
    $output = str_replace("&amp;","&",$output);
    $output = str_replace("&gt;",">",$output);
    $output = str_replace("&lt;","<",$output);
    $output = str_replace("&quot;","\"",$output);
    echo $output;
  }
}
?>